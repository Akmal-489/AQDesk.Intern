<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\UserSkill;
use App\Models\SkillTestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{
    public function index()
    {
        return response()->json(Skill::orderBy('skill_name')->get());
    }

    public function mySkills(Request $request)
    {
        $skills = UserSkill::with('skill')->where('user_id', $request->user()->id)->get();
        return response()->json($skills);
    }

    public function submitTest(Request $request, $skill_id)
    {
        $validator = Validator::make($request->all(), [
            'score' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        SkillTestAttempt::create([
            'user_id' => $request->user()->id,
            'skill_id' => $skill_id,
            'score' => $request->score,
            'attempted_at' => now(),
        ]);

        $userSkill = UserSkill::updateOrCreate(
            ['user_id' => $request->user()->id, 'skill_id' => $skill_id],
            ['percentage' => $request->score, 'updated_at' => now()]
        );

        $skillName = optional(Skill::find($skill_id))->skill_name;

        return response()->json([
            'message' => 'Test submitted. Your ' . $skillName . ' skill is now ' . $request->score . '%.',
            'user_skill' => $userSkill->load('skill'),
        ]);
    }
}