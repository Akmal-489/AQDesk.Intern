<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeeklyTestSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    // Upcoming tests only, soonest first
    public function index()
    {
        return response()->json(
            WeeklyTestSchedule::with('skill')
                ->whereDate('test_date', '>=', today())
                ->orderBy('test_date')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skill_id' => 'required|exists:skills,skill_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'test_date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule = WeeklyTestSchedule::create($request->only(['skill_id', 'title', 'description', 'test_date']));

        return response()->json(['message' => 'Test scheduled.', 'schedule' => $schedule], 201);
    }
}