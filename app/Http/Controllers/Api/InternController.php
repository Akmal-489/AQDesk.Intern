<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipApplication;
use App\Models\InternshipStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InternController extends Controller
{
    /** Direct apply for company-hosted internships */
    public function apply(Request $r, $internshipId)
    {
        $i = Internship::where('internship_id', $internshipId)->firstOrFail();

        if ($i->status !== 'published')
            return response()->json(['message' => 'This internship is not open.'], 422);
        if ($i->application_deadline && $i->application_deadline->endOfDay()->isPast())
            return response()->json(['message' => 'The application deadline has passed.'], 422);
        if ($i->posted_by == $r->user()->id)
            return response()->json(['message' => 'You cannot apply to your own internship.'], 422);

        $exists = InternshipApplication::where('internship_id', $i->internship_id)
            ->where('user_id', $r->user()->id)->exists();
        if ($exists)
            return response()->json(['message' => 'You have already applied.'], 409);

        InternshipApplication::create([
            'internship_id' => $i->internship_id,
            'user_id'       => $r->user()->id,
            'status'        => 'applied',
            'applied_at'    => now(),
        ]);
        return response()->json(['message' => 'Application submitted'], 201);
    }

    /** My applications + workflow stages + certificate */
    public function mine(Request $r)
    {
        return InternshipApplication::where('user_id', $r->user()->id)
            ->with(['internship', 'stages', 'certificate'])
            ->orderByDesc('applied_at')->get()
            ->map(fn ($a) => array_merge($a->toArray(), ['id' => $a->getKey()]));
    }

    public function submit(Request $r, $stageId)
    {
        $stage = InternshipStage::findOrFail($stageId);
        $app = InternshipApplication::findOrFail($stage->application_id);

        if ($app->user_id != $r->user()->id) abort(404);          // not yours
        if ($app->completion_status !== 'in_progress' || $stage->status !== 'open')
            return response()->json(['message' => 'This stage is not open for submission.'], 422);

        $r->validate([
            'submission_text' => 'required|string|max:10000',
            'file'            => 'nullable|file|mimes:pdf,zip,doc,docx,txt|max:5120',
        ]);

        $path = $r->hasFile('file')
            ? $r->file('file')->store('stage_submissions', 'local')   // PRIVATE storage
            : null;

        $stage->update([
            'submission_text' => $r->submission_text,
            'submission_path' => $path ?? $stage->submission_path,
            'submitted_at'    => now(),
            'status'          => 'submitted',
        ]);
        return ['message' => 'Submitted for review'];
    }

    /** Only the intern, the provider who owns the internship, or an admin */
    public function file(Request $r, $stageId)
    {
        $stage = InternshipStage::findOrFail($stageId);
        $app = InternshipApplication::findOrFail($stage->application_id);
        $u = $r->user();

        $isOwner    = $app->user_id == $u->id;
        $isProvider = Internship::where('internship_id', $app->internship_id)
                        ->where('posted_by', $u->id)->exists();
        
        $isAdmin = optional($u->role)->role_name === 'admin';
        if (!($isOwner || $isProvider || $isAdmin)) abort(404);

        if (!$stage->submission_path || !Storage::disk('local')->exists($stage->submission_path)) abort(404);
        return Storage::disk('local')->download($stage->submission_path);
    }
}