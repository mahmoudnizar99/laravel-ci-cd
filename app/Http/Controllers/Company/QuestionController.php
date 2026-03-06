<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyAnswer;
use App\Models\CompanyQuestion;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    public function index()
    {
        $questions = CompanyQuestion::all();
        return response()->json($questions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:company_questions,id',
            'answers.*.answer' => 'required|string',
        ]);

        $company = auth()->user()->company;

        // Delete existing answers if any
        $company->answers()->delete();

        // Create new answers
        foreach ($request->answers as $answer) {
            CompanyAnswer::create([
                'company_id' => $company->id,
                'question_id' => $answer['question_id'],
                'answer' => $answer['answer'],
            ]);
        }

        return response()->json(['message' => 'Answers submitted successfully'], 201);
    }
}