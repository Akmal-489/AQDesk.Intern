<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Validator;
use App\Models\Answer;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'nullable|string|max:50',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

        $question = Question::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'body' => $request->body,
            'category' => $request->category,
            'views' => 0,
        ]);

        return response()->json([
            'message' => 'Question posted successfully.',
            'question' => $question->load('user'),
        ], 201);
    }

    public function index()
    {
        $questions = Question::with('user', 'answers.user')
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($questions);
    }

    public function storeAnswer(Request $request, $question_id)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

        $answer = Answer::create([
            'question_id' => $question_id,
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        return response()->json([
            'message'=>'Answer posted successfully.',
            'answer'=>$answer->load('user'),
        ], 201);
    }
}
