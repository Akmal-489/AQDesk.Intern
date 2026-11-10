<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Internship;
use App\Models\InternshipApplication;
use App\Models\InternshipStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // The 5 stages every intern goes through
    private const STAGES = [
        [1, 'task',             'Practical Task 1', 'Complete the first practical task and submit your work (text and an optional file).'],
        [2, 'task',             'Practical Task 2', 'Build on Task 1 and submit your work.'],
        [3, 'task',             'Practical Task 3', 'Complete the final practical task and submit your work.'],
        [4, 'performance_test', 'Performance Test', 'Complete the performance test and submit your solution or a link to it.'],
        [5, 'theory_interview', 'Theory Test and Final Interview', 'Answer the theory test. The interview time and link appear here once scheduled.'],
    ];

    // Name of the primary key column of internship_applications
    private function pk(): string
    {
        return (new InternshipApplication)->getKeyName();
    }

    private function d($v)
    {
        return $v ? Carbon::parse($v)->format('Y-m-d') : null;
    }

    private function fail(string $msg, int $code = 422)
    {
        return response()->json(['message' => $msg], $code);
    }

    // =====================  OVERVIEW  =====================

    public function overview()
    {
        $pk = $this->pk();

        $byRole = DB::table('users as u')->join('roles as r', 'r.role_id', '=', 'u.role_id')
            ->selectRaw('r.role_name, count(*) as c')->groupBy('r.role_name')->pluck('c', 'role_name');
        $totalUsers = (int) $byRole->sum();
        $employers  = (int) ($byRole['employer'] ?? 0);
        $admins     = (int) ($byRole['admin'] ?? 0);

        $awaiting = InternshipApplication::where('status', 'accepted')
            ->where(function ($q) {
                $q->whereNull('completion_status')->orWhere('completion_status', 'not_started');
            })->count();

        $stats = [
            'employees'         => $totalUsers - $employers - $admins,
            'employers'         => $employers,
            'pending_companies' => DB::table('employer_profiles')->where('approval_status', 'pending')->count(),
            'open_internships'  => Internship::where('status', 'published')->count(),
            'applications'      => InternshipApplication::count(),
            'awaiting_start'    => $awaiting,
            'in_program'        => InternshipApplication::where('completion_status', 'in_progress')->count(),
            'completed'         => InternshipApplication::where('completion_status', 'completed')->count(),
            'certificates'      => Certificate::where('status', 'valid')->count(),
        ];

        $notes = collect();

        foreach (DB::table('employer_profiles')->where('approval_status', 'pending')
                    ->orderByDesc('created_at')->limit(5)->get() as $p) {
            $notes->push(['icon' => '🏢', 'text' => "New company waiting for approval: {$p->company_name}",
                          'time' => $p->created_at, 'tab' => 'companies']);
        }

        $recent = DB::table('internship_applications as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->join('internships as i', 'i.internship_id', '=', 'a.internship_id')
            ->orderByDesc('a.applied_at')->limit(6)
            ->select('u.name', 'i.title', 'a.applied_at')->get();
        foreach ($recent as $x) {
            $notes->push(['icon' => '🎓', 'text' => "{$x->name} applied to {$x->title}",
                          'time' => $x->applied_at, 'tab' => 'programs']);
        }

        $subs = DB::table('internship_stages as s')
            ->join('internship_applications as a', "a.$pk", '=', 's.application_id')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('s.status', 'submitted')->orderByDesc('s.submitted_at')->limit(6)
            ->select('u.name', 's.title', 's.submitted_at')->get();
        foreach ($subs as $x) {
            $notes->push(['icon' => '📎', 'text' => "{$x->name} submitted: {$x->title}",
                          'time' => $x->submitted_at, 'tab' => 'programs']);
        }

        if ($awaiting) {
            $notes->push(['icon' => '⏳', 'text' => "$awaiting accepted candidate(s) are waiting for their 5-stage program to start",
                          'time' => now()->toDateTimeString(), 'tab' => 'programs']);
        }

        return [
            'stats' => $stats,
            'notes' => $notes->sortByDesc(fn ($n) => (string) $n['time'])->values()->take(12),
        ];
    }

    // =====================  COMPANIES  =====================

    public function employers()
    {
        return DB::table('employer_profiles as p')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->orderByRaw("FIELD(p.approval_status, 'pending', 'rejected', 'approved')")
            ->orderByDesc('p.created_at')->limit(200)
            ->select('p.user_id', 'p.company_name', 'p.website', 'p.industry', 'p.company_size', 'p.city',
                     'p.country', 'p.phone', 'p.registration_no', 'p.description', 'p.contact_designation',
                     'p.approval_status', 'p.rejection_reason', 'p.created_at',
                     'u.name as contact_name', 'u.email')
            ->get();
    }

    public function approveEmployer(Request $r, $userId)
    {
        $q = DB::table('employer_profiles')->where('user_id', $userId);
        abort_unless($q->exists(), 404);
        $q->update([
            'approval_status'  => 'approved',
            'rejection_reason' => null,
            'reviewed_by'      => $r->user()->getKey(),
            'reviewed_at'      => now(),
            'updated_at'       => now(),
        ]);
        return ['message' => 'Company approved.'];
    }

    public function rejectEmployer(Request $r, $userId)
    {
        $r->validate(['reason' => 'required|string|max:255']);
        $q = DB::table('employer_profiles')->where('user_id', $userId);
        abort_unless($q->exists(), 404);
        $q->update([
            'approval_status'  => 'rejected',
            'rejection_reason' => $r->reason,
            'reviewed_by'      => $r->user()->getKey(),
            'reviewed_at'      => now(),
            'updated_at'       => now(),
        ]);
        return ['message' => 'Company rejected.'];
    }

    // =====================  INTERNS & STAGES  =====================

    /** Accepted candidates and everyone already in the program */
    public function programs()
    {
        $pk = $this->pk();

        $rows = DB::table('internship_applications as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->join('internships as i', 'i.internship_id', '=', 'a.internship_id')
            ->where(function ($q) {
                $q->where('a.status', 'accepted')
                  ->orWhereIn('a.completion_status', ['in_progress', 'completed', 'unsuccessful']);
            })
            ->orderByDesc('a.applied_at')->limit(200)
            ->select("a.$pk as id", 'a.status', 'a.completion_status', 'u.name', 'u.email', 'i.title', 'i.company_name')
            ->get();

        $ids    = $rows->pluck('id');
        $passed = DB::table('internship_stages')->whereIn('application_id', $ids)->where('status', 'passed')
                    ->selectRaw('application_id, count(*) as c')->groupBy('application_id')->pluck('c', 'application_id');
        $total  = DB::table('internship_stages')->whereIn('application_id', $ids)
                    ->selectRaw('application_id, count(*) as c')->groupBy('application_id')->pluck('c', 'application_id');
        $certs  = Certificate::whereIn('application_id', $ids)->pluck('certificate_no', 'application_id');

        return $rows->map(fn ($x) => [
            'id'                => $x->id,
            'status'            => $x->status,
            'completion_status' => $x->completion_status,
            'name'              => $x->name,
            'email'             => $x->email,
            'title'             => $x->title,
            'company'           => $x->company_name ?: 'AQDesk',
            'passed'            => (int) ($passed[$x->id] ?? 0),
            'stages'            => (int) ($total[$x->id] ?? 0),
            'certificate'       => $certs[$x->id] ?? null,
        ])->values();
    }

    /** One intern's full program */
    public function program($id)
    {
        $a = InternshipApplication::findOrFail($id);
        $u = User::find($a->user_id);
        $i = Internship::where('internship_id', $a->internship_id)->first();

        return [
            'id'                => $a->getKey(),
            'status'            => $a->status,
            'completion_status' => $a->completion_status,
            'student'           => ['name' => $u->name ?? 'Unknown', 'email' => $u->email ?? ''],
            'internship'        => ['title' => $i->title ?? 'Internship', 'company_name' => $i->company_name ?? 'AQDesk'],
            'stages'            => InternshipStage::where('application_id', $a->getKey())->orderBy('stage_no')->get(),
            'certificate'       => Certificate::where('application_id', $a->getKey())->first(),
        ];
    }

    /** Create the 5 stages (stage 1 opens, the rest stay locked) */
    public function startProgram($id)
    {
        $a = InternshipApplication::findOrFail($id);

        if ($a->status !== 'accepted')
            return $this->fail('Only accepted candidates can start the program.');
        if (InternshipStage::where('application_id', $a->getKey())->exists())
            return $this->fail('The program has already started.');

        DB::transaction(function () use ($a) {
            foreach (self::STAGES as [$no, $type, $title, $text]) {
                (new InternshipStage)->forceFill([
                    'application_id' => $a->getKey(),
                    'stage_no'       => $no,
                    'stage_type'     => $type,
                    'title'          => $title,
                    'instructions'   => $text,
                    'status'         => $no === 1 ? 'open' : 'locked',
                ])->save();
            }
            $a->forceFill(['completion_status' => 'in_progress', 'current_stage' => 1])->save();
        });

        return ['message' => 'The 5-stage program has started.'];
    }

    /** Edit a stage's title, instructions, due date, interview time and link */
    public function updateStage(Request $r, $id)
    {
        $s = InternshipStage::findOrFail($id);
        $d = $r->validate([
            'title'          => 'required|string|max:150',
            'instructions'   => 'nullable|string|max:5000',
            'due_date'       => 'nullable|date',
            'interview_at'   => 'nullable|date',
            'interview_link' => 'nullable|url|max:255',
        ]);

        $data = [
            'title'        => $d['title'],
            'instructions' => $d['instructions'] ?? null,
            'due_date'     => $d['due_date'] ?? null,
        ];
        if ($s->stage_no == 5) {
            $data['interview_at']   = $d['interview_at'] ?? null;
            $data['interview_link'] = $d['interview_link'] ?? null;
        }
        $s->forceFill($data)->save();

        return $s;
    }

    /** Pass or fail a stage with a score and feedback. A pass opens the next stage. */
    public function reviewStage(Request $r, $id)
    {
        $s = InternshipStage::findOrFail($id);
        $a = InternshipApplication::findOrFail($s->application_id);
        $d = $r->validate([
            'result'   => 'required|in:pass,fail',
            'score'    => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:2000',
        ]);

        if ($a->completion_status !== 'in_progress' || !in_array($s->status, ['open', 'submitted']))
            return $this->fail('This stage cannot be reviewed right now.');

        DB::transaction(function () use ($s, $a, $d) {
            $s->forceFill([
                'status'      => $d['result'] === 'pass' ? 'passed' : 'failed',
                'score'       => $d['score'],
                'feedback'    => $d['feedback'] ?? null,
                'reviewed_at' => now(),
            ])->save();

            if ($d['result'] === 'pass') {
                $next = InternshipStage::where('application_id', $a->getKey())
                    ->where('stage_no', $s->stage_no + 1)->first();
                if ($next && $next->status === 'locked') {
                    $next->forceFill(['status' => 'open'])->save();
                    $a->forceFill(['current_stage' => $next->stage_no])->save();
                }
            }
        });

        return ['message' => 'Stage reviewed.'];
    }

    /** Let the intern try a failed stage again */
    public function reopenStage($id)
    {
        $s = InternshipStage::findOrFail($id);
        $a = InternshipApplication::findOrFail($s->application_id);

        if ($s->status !== 'failed' || $a->completion_status !== 'in_progress')
            return $this->fail('Only a failed stage of an active program can be reopened.');

        $s->forceFill(['status' => 'open'])->save();
        return ['message' => 'Stage reopened. The intern can submit again.'];
    }

    /** Mark the whole internship as completed (all 5 stages must be passed) */
    public function completeProgram($id)
    {
        $a = InternshipApplication::findOrFail($id);
        if ($a->completion_status !== 'in_progress')
            return $this->fail('This program is not in progress.');

        $stages = InternshipStage::where('application_id', $a->getKey())->get();
        if ($stages->count() !== 5 || $stages->where('status', 'passed')->count() !== 5)
            return $this->fail('All 5 stages must be passed before the internship can be completed.');

        $a->forceFill(['completion_status' => 'completed', 'completed_at' => now()])->save();
        return ['message' => 'Internship marked as completed.'];
    }

    /** Mark the internship as not successful (no certificate) */
    public function failProgram($id)
    {
        $a = InternshipApplication::findOrFail($id);
        if ($a->completion_status !== 'in_progress')
            return $this->fail('This program is not in progress.');

        $a->forceFill(['completion_status' => 'unsuccessful'])->save();
        return ['message' => 'Internship marked as not successful.'];
    }

    private function newNumber(): string
    {
        do {
            $n = 'AQD-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (Certificate::where('certificate_no', $n)->exists());
        return $n;
    }

    /** Issue the certificate (only after completion, only once) */
    public function issueCertificate(Request $r, $id)
    {
        $a = InternshipApplication::findOrFail($id);

        if ($a->completion_status !== 'completed')
            return $this->fail('Mark the internship as completed first.');
        if (Certificate::where('application_id', $a->getKey())->exists())
            return $this->fail('A certificate was already issued for this internship.');

        $u = User::findOrFail($a->user_id);
        $i = Internship::where('internship_id', $a->internship_id)->firstOrFail();

        $c = DB::transaction(function () use ($a, $u, $i, $r) {
            return Certificate::create([
                'certificate_no'  => $this->newNumber(),
                'application_id'  => $a->getKey(),
                'user_id'         => $u->getKey(),
                'internship_id'   => $i->internship_id,
                'candidate_name'  => $u->name,
                'internship_title'=> $i->title,
                'company_name'    => $i->company_name ?: 'AQDesk',
                'completion_date' => $a->completed_at ? Carbon::parse($a->completed_at)->toDateString() : now()->toDateString(),
                'issued_by'       => $r->user()->getKey(),
                'issued_at'       => now(),
                'status'          => 'valid',
            ]);
        });

        return response()->json($c, 201);
    }

    // =====================  CERTIFICATES  =====================

    public function certificates()
    {
        return Certificate::orderByDesc('certificate_id')->limit(200)->get();
    }

    public function setCertificateStatus(Request $r, $id)
    {
        $r->validate(['status' => 'required|in:valid,revoked']);
        $c = Certificate::findOrFail($id);
        $c->forceFill(['status' => $r->status])->save();
        return ['message' => $r->status === 'revoked' ? 'Certificate revoked.' : 'Certificate restored.'];
    }

    // =====================  INTERNSHIPS  =====================

    public function internships()
    {
        $list   = Internship::orderByDesc('internship_id')->limit(200)->get();
        $counts = InternshipApplication::whereIn('internship_id', $list->pluck('internship_id'))
                    ->selectRaw('internship_id, count(*) as c')->groupBy('internship_id')->pluck('c', 'internship_id');
        $posters = User::whereIn('id', $list->pluck('posted_by')->filter()->unique())->pluck('name', 'id');

        return $list->map(fn ($i) => [
            'internship_id'        => $i->internship_id,
            'title'                => $i->title,
            'company_name'         => $i->company_name ?: 'AQDesk',
            'hosting_type'         => $i->hosting_type,
            'status'               => $i->status,
            'application_deadline' => $this->d($i->application_deadline),
            'applicants_count'     => (int) ($counts[$i->internship_id] ?? 0),
            'posted_by_name'       => $posters[$i->posted_by] ?? '—',
        ])->values();
    }

    public function setInternshipStatus(Request $r, $id)
    {
        $r->validate(['status' => 'required|in:published,closed']);
        $i = Internship::where('internship_id', $id)->firstOrFail();
        $i->forceFill(['status' => $r->status])->save();
        return ['message' => $r->status === 'closed' ? 'Internship closed.' : 'Internship reopened.'];
    }

    public function deleteInternship($id)
    {
        $i = Internship::where('internship_id', $id)->firstOrFail();
        if (InternshipApplication::where('internship_id', $i->internship_id)->exists())
            return $this->fail('This internship has applicants, so it cannot be deleted. Close it instead.');
        $i->delete();
        return ['message' => 'Internship deleted.'];
    }

    // =====================  USERS  =====================

    public function users()
    {
        $hasStatus = Schema::hasColumn('users', 'status');
        $cols = ['u.id', 'u.name', 'u.email', 'r.role_name', 'u.created_at'];
        if ($hasStatus) $cols[] = 'u.status';

        return DB::table('users as u')->leftJoin('roles as r', 'r.role_id', '=', 'u.role_id')
            ->orderByDesc('u.id')->limit(300)->select($cols)->get()
            ->map(function ($u) {
                $u->status = $u->status ?? 'active';
                return $u;
            });
    }

    public function setUserStatus(Request $r, $id)
    {
        $r->validate(['status' => 'required|in:active,suspended']);
        if (!Schema::hasColumn('users', 'status'))
            return $this->fail("The users table has no 'status' column yet. Run the ALTER TABLE from the setup step.");

        $t = User::findOrFail($id);
        if ($t->getKey() == $r->user()->getKey())
            return $this->fail('You cannot change your own account.');
        if (optional($t->role)->role_name === 'admin')
            return $this->fail('Admin accounts cannot be suspended here.');

        $t->forceFill(['status' => $r->status])->save();
        if ($r->status === 'suspended') $t->tokens()->delete();   // log them out everywhere

        return ['message' => $r->status === 'suspended' ? 'User suspended.' : 'User activated.'];
    }
}