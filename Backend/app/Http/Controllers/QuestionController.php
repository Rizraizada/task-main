<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        return response()->json(Question::with('chapter')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'text' => 'required|string|max:255',
            'question_text' => 'required|string|max:1000',
        ]);

        $question = Question::create($validated);

        return response()->json([
            'message' => 'Question created successfully',
            'question' => $question,
        ], 201);
    }

    public function show(Question $question)
    {
        return response()->json($question->load('chapter'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'chapter_id' => 'nullable|exists:chapters,id',
            'text' => 'nullable|string|max:255',
            'question_text' => 'nullable|string|max:1000',
        ]);

        $question->update($validated);

        return response()->json([
            'message' => 'Question updated successfully',
            'question' => $question,
        ]);
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully',
        ]);
    }
}
