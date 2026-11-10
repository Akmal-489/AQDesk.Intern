<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EmployerController extends Controller
{
    // The logged-in employer's company profile (or null)
    private function profile(Request $r)
    {
        return DB::table('employer_profiles')->where('user_id', $r->user()->getKey())->first();
    }

    // Stops companies that are pending, rejected or have no profile
    private function approvedProfile(Request $r)
    {
        $p = $this->profile($r);
        if (!$p || $p->approval_status !== 'approved') {
            abort(response()->json(['message' => 'Your company must be approved by an admin before you can do this.'], 403));
        }
        return $p;
    }

    // An internship that belongs to this employer, or 404
    private function owned(Request $r, $id): Internship
    {
        return Internship::where('internship_id', $id)
            ->where('posted_by', $r->user()->getKey())->firstOrFail();
    }

    private function d($v)
    {
        return $v ? Carbon::parse($v)->format('Y-m-d') : null;
    }

    // Add https:// if the employer forgot it
    private function prep(Request $r): void
    {
        if ($r->filled('apply_link') && !preg_match('~^https?://~i', $r->apply_link)) {
            $r->merge(['apply_link' => 'https://' . $r->apply_link]);
        }
    }

    private function rules(bool $creating): array
    {
        return [
            'title'                => 'required|string|max:100',
            'description'          => 'required|string|max:255',
            'specifications'       => 'required|string|min:20|max:5000',
            'category'             => 'nullable|string|max:50',
            'duration_weeks'       => 'required|integer|min:1|max:52',
            'application_deadline' => $creating ? 'required|date|after_or_equal:today' : 'required|date',
            'work_mode'            => 'required|in:onsite,remote,hybrid',
            'location'             => 'nullable|string|max:100',
            'start_date'           => 'nullable|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'apply_link'           => 'nullable|url|max:255',
            'is_paid'              => 'required|boolean',
            'stipend'              => 'nullable|string|max:60',
        ];
    }

    private function fields(array $d): array
    {
        return [
            'title'                => $d['title'],
            'description'          => $d['description'],
            'specifications'       => $d['specifications'],
            'category'             => strtolower(trim($d['category'] ?? '')) ?: 'other',
            'duration_weeks'       => $d['duration_weeks'],
            'application_deadline' => $d['application_deadline'],
            'work_mode'            => $d['work_mode'],
            'location'             => $d['location'] ?? null,
            'start_date'           => $d['start_date'] ?? null,
            'end_date'             => $d['end_date'] ?? null,
            'apply_link'           => $d['apply_link'] ?? null,
            'is_paid'              => (int) $d['is_paid'],
            'stipend'              => $d['stipend'] ?? null,
        ];
    }

    /** Company info + approval status (feeds the banner) */
    public function me(Request $r)
    {
        $p = $this->profile($r);
        return [
            'name'             => $r->user()->name,
            'email'            => $r->user()->email,
            'approval_status'  => $p ? $p->approval_status : 'missing',
            'rejection_reason' => $p->rejection_reason ?? null,
            'company_name'     => $p->company_name ?? null,
            'website'          => $p->website ?? null,
            'industry'         => $p->industry ?? null,
            'city'             => $p->city ?? null,
        ];
    }

    /** My internships, with an applicant count each */
    public function index(Request $r)
    {
        $list = Internship::where('posted_by', $r->user()->getKey())
            ->where('hosting_type', 'company')
            ->orderByDesc('internship_id')->get();

        $counts = InternshipApplication::whereIn('internship_id', $list->pluck('internship_id'))
            ->selectRaw('internship_id, count(*) as c')->groupBy('internship_id')
            ->pluck('c', 'internship_id');

        return $list->map(fn ($i) => array_merge($i->toArray(), [
            'application_deadline' => $this->d($i->application_deadline),
            'start_date'           => $this->d($i->start_date),
            'end_date'             => $this->d($i->end_date),
            'applicants_count'     => (int) ($counts[$i->internship_id] ?? 0),
        ]));
    }

    /** Post an internship (approved companies only) */
    public function store(Request $r)
    {
        $p = $this->approvedProfile($r);
        $this->prep($r);
        $d = $r->validate($this->rules(true));

        $i = new Internship();
        $i->forceFill($this->fields($d) + [
            'company_name'        => $p->company_name,   // always the real company name
            'hosting_type'        => 'company',
            'status'              => 'published',
            'posted_by'           => $r->user()->getKey(),
            'required_skill_id'   => null,
            'required_percentage' => 0,
        ])->save();

        return response()->json($i, 201);
    }

    /** Edit my own internship */
    public function update(Request $r, $id)
    {
        $this->approvedProfile($r);
        $i = $this->owned($r, $id);
        $this->prep($r);
        $d = $r->validate($this->rules(false));

        $i->forceFill($this->fields($d))->save();
        return $i;
    }

    /** Close or reopen my internship */
    public function setStatus(Request $r, $id)
    {
        $this->approvedProfile($r);
        $i = $this->owned($r, $id);
        $r->validate(['status' => 'required|in:published,closed']);

        $i->forceFill(['status' => $r->status])->save();
        return ['message' => 'Updated', 'status' => $i->status];
    }

    /** Applicants of one of my internships */
    public function applicants(Request $r, $id)
    {
        $i = $this->owned($r, $id);

        $apps = InternshipApplication::where('internship_id', $i->internship_id)
            ->orderByDesc('applied_at')->get();

        $ids      = $apps->pluck('user_id')->all();
        $users    = User::whereIn('id', $ids)->get()->keyBy('id');
        $profiles = DB::table('employee_profiles')->whereIn('user_id', $ids)->get()->keyBy('user_id');

        return $apps->map(function ($a) use ($users, $profiles) {
            $u = $users[$a->user_id] ?? null;
            $p = $profiles[$a->user_id] ?? null;
            return [
                'application_id'    => $a->getKey(),
                'status'            => $a->status,
                'completion_status' => $a->completion_status,
                'applied_at'        => $a->applied_at ? Carbon::parse($a->applied_at)->format('Y-m-d') : null,
                'name'              => $u->name ?? 'Unknown',
                'email'             => $u->email ?? '',
                'university'        => $p->university ?? null,
                'field_of_study'    => $p->field_of_study ?? null,
                'city'              => $p->city ?? null,
            ];
        })->values();
    }

    /** Shortlist, accept or reject an applicant */
    public function applicantStatus(Request $r, $id)
    {
        $this->approvedProfile($r);
        $r->validate(['status' => 'required|in:shortlisted,accepted,rejected']);

        $a = InternshipApplication::findOrFail($id);

        // the application must belong to one of MY internships
        Internship::where('internship_id', $a->internship_id)
            ->where('posted_by', $r->user()->getKey())->firstOrFail();

        if (in_array($a->completion_status, ['in_progress', 'completed', 'unsuccessful'])) {
            return response()->json(['message' => 'This candidate is already in the AQDesk program and is managed by the admin.'], 422);
        }

        $a->status = $r->status;
        $a->save();
        return ['message' => 'Updated', 'status' => $a->status];
    }
}