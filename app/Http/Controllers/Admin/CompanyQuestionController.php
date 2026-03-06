<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyQuestion;
use Illuminate\Http\Request;

class CompanyQuestionController extends Controller
{
    public function index()
    {
        $questions = CompanyQuestion::all();
        return response()->json($questions);
    }

    public function store(Request $request)
    {
        $request->validate(['question' => 'required|string|max:255']);

        $question = CompanyQuestion::create($request->only('question'));

        return response()->json($question, 201);
    }

    public function update(Request $request, $id)
    {
        $question = CompanyQuestion::findOrFail($id);

        $request->validate(['question' => 'required|string|max:255']);

        $question->update($request->only('question'));

        return response()->json($question);
    }

    public function destroy($id)
    {
        $question = CompanyQuestion::findOrFail($id);
        $question->delete();

        return response()->json(null, 204);
    }
}