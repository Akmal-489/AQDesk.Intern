<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipApplication;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InternshipController extends Controller
{
    public function index()
    {
        return response()->json(Internship::with('required_skill')
        ->where('status', 'published')
        ->where(function ($q) {$q->whereNull('application_deadline')
        ->orWhere('application_deadline', '>=', now()->toDateString());
        })
        ->orderBy('created_at', 'desc')
        ->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'required|in:web_development,software_development,other',
            'description' => 'required|string',
            'duration_weeks' => 'required|integer|min:1',
            'required_skill_id' => 'required|exists:skills,skill_id',
            'required_percentage' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $internship = Internship::create([
            'posted_by' => $request->user()->id,
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'duration_weeks' => $request->duration_weeks,
            'required_skill_id' => $request->required_skill_id,
            'required_percentage' => $request->required_percentage,
        ]);

        return response()->json(['message' => 'Internship posted.', 'internship' => $internship], 201);
    }

    public function myApplications(Request $request)
    {
        $apps = InternshipApplication::with('internship')->where('user_id', $request->user()->id)->get();
        return response()->json($apps);
    }

    // "Conduct Test" button — just logs that the user applied and is going to take the test
    public function applyForTest(Request $request, $internship_id)
    {
        $internship = Internship::findOrFail($internship_id);

        $application = InternshipApplication::firstOrCreate(
            ['internship_id' => $internship_id, 'user_id' => $request->user()->id],
            ['status' => 'applied', 'applied_at' => now()]
        );

        return response()->json([
            'message' => 'Applied. Please complete the ' . optional($internship->required_skill)->skill_name . ' test.',
            'application' => $application,
        ]);
    }

    // "Appear in Interview" button — checks skill percentage against the internship's requirement
    public function requestInterview(Request $request, $internship_id)
    {
        $internship = Internship::findOrFail($internship_id);

        $userSkill = UserSkill::where('user_id', $request->user()->id)
            ->where('skill_id', $internship->required_skill_id)
            ->first();

        $currentPercentage = $userSkill->percentage ?? 0;

        if ($currentPercentage < $internship->required_percentage) {
            return response()->json([
                'eligible' => false,
                'message' => 'Your ' . optional($internship->required_skill)->skill_name . ' skill is ' . $currentPercentage . '%. You need at least ' . $internship->required_percentage . '%. Please conduct the test first.',
            ]);
        }

        $application = InternshipApplication::updateOrCreate(
            ['internship_id' => $internship_id, 'user_id' => $request->user()->id],
            ['status' => 'pending', 'applied_at' => now()]
        );

        return response()->json([
            'eligible' => true,
            'message' => 'Eligibility criteria met! Your interview request is now pending review.',
            'application' => $application,
        ]);
    }
}