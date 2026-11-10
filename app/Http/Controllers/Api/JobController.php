<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:job,internship',
            'description' => 'required|string',
            'deadline' => 'nullable|date',
            'registration_link' => 'nullable|url',
            'advertisement_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pdfPath = null;
        if ($request->hasFile('advertisement_pdf')) {
            $pdfPath = $request->file('advertisement_pdf')->store('job-ads', 'public');
        }

        $job = JobPosting::create([
            'posted_by' => $request->user()->id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'registration_link' => $request->registration_link,
            'advertisement_pdf' => $pdfPath,
        ]);

        return response()->json([
            'message' => 'Job posted successfully.',
            'job' => $job->load('poster'),
        ], 201);
    }

    public function index()
    {
        $job = JobPosting::with('poster', 'applications.applicant')
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($job);
    }

    public function apply(Request $request, $job_id)
    {
        $validator = Validator::make($request->all(), [
            'resume_note' => 'nullable|string',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $application = JobApplication::create([
            'job_id' => $job_id,
            'applicant_id' => $request->user()->id,
            'resume_note' => $request->resume_note,
            'status' => 'applied',
        ]);

        return response()->json([
            'message' => 'Application submitted successfully.',
            'application' => $application,
        ], 201);
    }
}