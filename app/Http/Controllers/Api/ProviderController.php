<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Internship;
use App\Models\InternshipApplication;
use App\Models\InternshipStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProviderController extends Controller
{
    private const DEFAULT_STAGES = [
        [1, 'task',             'Stage 1: Practical Task 1'],
        [2, 'task',             'Stage 2: Practical Task 2'],
        [3, 'task',             'Stage 3: Practical Task 3'],
        [4, 'performance_test', 'Stage 4: Performance-Based Test'],
        [5, 'theory_interview', 'Stage 5: Theory Test & Final Interview'],
    ];

    /* ---------- ownership helpers: a provider only touches their own data ---------- */
    private function isAdmin(Request $r) { return $r->user()->role_id == 6; }

    private function ownedInternship(Request $r, $id)
    {
        $q = Internship::where('internship_id', $id);
        if (!$this->isAdmin($r)) $q->where('posted_by', $r->user()->id);
        return $q->firstOrFail();
    }

    private function ownedApp(Request $r, $id)
    {
        $app = InternshipApplication::findOrFail($id);
        $this->ownedInternship($r, $app->internship_id);   // 404 if not the owner
        return $app;
    }

    private function ownedStage(Request $r, $id)
    {
        $stage = InternshipStage::findOrFail($id);
        $app = $this->ownedApp($r, $stage->application_id);
        return [$stage, $app];
    }

    /* ---------- postings ---------- */
    private function rules(bool $creating): array
    {
        return [
            'company_name'         => 'required|string|max:150',
            'title'                => 'required|string|max:255',
            'category'             => 'required|string|max:80',
            'description'          => 'required|string|max:3000',
            'specifications'       => 'required|string|max:5000',
            'application_deadline' => $creating ? 'required|date|after_or_equal:today' : 'required|date',
            'duration_weeks'       => 'required|integer|min:1|max:104',
            'start_date'           => 'nullable|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'work_mode'            => 'required|in:onsite,remote,hybrid',
            'location'             => 'nullable|string|max:120',
            'apply_link'           => 'nullable|url|max:255',
            'is_paid' => 'required|boolean',
            'stipend' => 'nullable|string|max:60',
        ];
    }

    public function index(Request $r)
    {
        $q = Internship::withCount('applications')->orderByDesc('internship_id');
        if (!$this->isAdmin($r)) $q->where('posted_by', $r->user()->id);
        return $q->get();
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules(true));
        $data += [
            'posted_by'           => $r->user()->id,
            'hosting_type'        => 'company',
            'status'              => 'draft',
            'required_skill_id'   => null,
            'required_percentage' => null,
            'is_paid' => 'required|boolean',
            'stipend' => 'nullable|string|max:60',
        ];
        return response()->json(Internship::create($data), 201);
    }

    public function update(Request $r, $id)
    {
        $i = $this->ownedInternship($r, $id);
        $i->update($r->validate($this->rules(false)));
        return $i;
    }

    public function setStatus(Request $r, $id)
    {
        $i = $this->ownedInternship($r, $id);
        $i->update($r->validate(['status' => 'required|in:draft,published,closed']));
        return $i;
    }

    /* ---------- applicants ---------- */
    public function applications(Request $r, $id)
    {
        $i = $this->ownedInternship($r, $id);
        return InternshipApplication::where('internship_id', $i->internship_id)
            ->with(['applicant:id,name,email,bio', 'stages', 'certificate'])
            ->orderByDesc('applied_at')->get()
            ->map(fn ($a) => array_merge($a->toArray(), ['id' => $a->getKey()]));
    }

    public function updateApplicationStatus(Request $r, $id)
    {
        $app = $this->ownedApp($r, $id);
        if ($app->stages()->exists()) {
            return response()->json(['message' => 'Workflow already started; status is locked.'], 422);
        }
        $app->update($r->validate(['status' => 'required|in:applied,pending,shortlisted,accepted,rejected']));
        return ['message' => 'Status updated'];
    }

    /* ---------- 5-stage workflow ---------- */
    public function startWorkflow(Request $r, $id)
    {
        $app = $this->ownedApp($r, $id);
        if ($app->status !== 'accepted')
            return response()->json(['message' => 'Accept the candidate first.'], 422);
        if ($app->stages()->exists())
            return response()->json(['message' => 'Workflow already started.'], 409);

        $data = $r->validate([
            'stages'                => 'nullable|array|max:5',
            'stages.*.title'        => 'nullable|string|max:150',
            'stages.*.instructions' => 'nullable|string|max:5000',
            'stages.*.due_date'     => 'nullable|date',
        ]);

        DB::transaction(function () use ($app, $data) {
            foreach (self::DEFAULT_STAGES as $i => [$no, $type, $title]) {
                $c = $data['stages'][$i] ?? [];
                InternshipStage::create([
                    'application_id' => $app->getKey(),
                    'stage_no'       => $no,
                    'stage_type'     => $type,
                    'title'          => $c['title'] ?? $title,
                    'instructions'   => $c['instructions'] ?? null,
                    'due_date'       => $c['due_date'] ?? null,
                    'status'         => $no === 1 ? 'open' : 'locked',
                ]);
            }
            $app->update(['current_stage' => 1, 'completion_status' => 'in_progress']);
        });
        return ['message' => 'Workflow started'];
    }

    public function updateStage(Request $r, $id)
    {
        [$stage] = $this->ownedStage($r, $id);
        if (in_array($stage->status, ['passed', 'failed']))
            return response()->json(['message' => 'This stage is already closed.'], 422);

        $data = $r->validate([
            'title'          => 'required|string|max:150',
            'instructions'   => 'nullable|string|max:5000',
            'due_date'       => 'nullable|date',
            'interview_at'   => 'nullable|date',
            'interview_link' => 'nullable|url|max:255',
        ]);
        if ($stage->stage_no !== 5) unset($data['interview_at'], $data['interview_link']);
        $stage->update($data);
        return $stage;
    }

    public function reviewStage(Request $r, $id)
    {
        [$stage, $app] = $this->ownedStage($r, $id);
        $data = $r->validate([
            'status'           => 'required|in:passed,failed',
            'score'            => 'nullable|numeric|min:0|max:100',
            'feedback'         => 'nullable|string|max:3000',
            'interview_result' => 'nullable|in:passed,failed',
        ]);

        if ($stage->status !== 'submitted')
            return response()->json(['message' => 'Only submitted stages can be reviewed.'], 422);

        if ($stage->stage_no === 5) {
            if (empty($data['interview_result']))
                return response()->json(['message' => 'Enter the interview result for Stage 5.'], 422);
            if ($data['status'] === 'passed' && $data['interview_result'] !== 'passed')
                return response()->json(['message' => 'Stage 5 passes only if the interview is also passed.'], 422);
        }

        DB::transaction(function () use ($stage, $app, $data) {
            $stage->update([
                'status'           => $data['status'],
                'score'            => $data['score'] ?? null,
                'feedback'         => $data['feedback'] ?? null,
                'interview_result' => $stage->stage_no === 5 ? $data['interview_result'] : $stage->interview_result,
                'reviewed_at'      => now(),
            ]);

            if ($data['status'] === 'passed' && $stage->stage_no < 5) {
                InternshipStage::where('application_id', $app->getKey())
                    ->where('stage_no', $stage->stage_no + 1)->update(['status' => 'open']);
                $app->update(['current_stage' => $stage->stage_no + 1]);
            } elseif ($data['status'] === 'failed') {
                $app->update(['completion_status' => 'unsuccessful', 'completed_at' => now()]);
            }
        });
        return ['message' => 'Stage reviewed'];
    }

    public function completeApplication(Request $r, $id)
    {
        $app = $this->ownedApp($r, $id);
        $data = $r->validate(['result' => 'required|in:completed,unsuccessful']);

        if ($app->completion_status === 'completed')
            return response()->json(['message' => 'Already completed.'], 422);

        if ($data['result'] === 'completed' &&
            $app->stages()->where('status', 'passed')->count() !== 5) {
            return response()->json(['message' => 'All 5 stages must be passed first.'], 422);
        }
        $app->update(['completion_status' => $data['result'], 'completed_at' => now()]);
        return ['message' => 'Marked as ' . $data['result']];
    }

    /* ---------- certificates ---------- */
    public function issueCertificate(Request $r, $id)
    {
        $app = $this->ownedApp($r, $id);
        if ($app->completion_status !== 'completed')
            return response()->json(['message' => 'Certificate can only be issued after successful completion.'], 422);
        if (Certificate::where('application_id', $app->getKey())->exists())
            return response()->json(['message' => 'Certificate already issued.'], 409);

        $internship = Internship::where('internship_id', $app->internship_id)->firstOrFail();
        $candidate  = $app->applicant;

        $cert = DB::transaction(function () use ($r, $app, $internship, $candidate) {
            do {
                $no = 'AQD-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
            } while (Certificate::where('certificate_no', $no)->exists());

            return Certificate::create([
                'certificate_no'   => $no,
                'application_id'   => $app->getKey(),
                'user_id'          => $app->user_id,
                'internship_id'    => $internship->internship_id,
                'candidate_name'   => $candidate->name,
                'internship_title' => $internship->title,
                'company_name'     => $internship->company_name ?: 'AQDesk',
                'completion_date'  => ($app->completed_at ?? now())->toDateString(),
                'issued_by'        => $r->user()->id,
                'issued_at'        => now(),
                'status'           => 'valid',
            ]);
        });
        return response()->json($cert, 201);
    }
}