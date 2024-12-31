<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index()
    {

        return response()->json(Chapter::with('questions')->get());


    }

    // Create a new chapter
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'number' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        $chapter = Chapter::create($validated);
        return response()->json([
            'message' => 'Chapter created successfully',
            'chapter' => $chapter
        ], 201);
    }

    // Get a single chapter with its questions
    public function show(Chapter $chapter)
    {
        $chapter->load('questions');
        return response()->json($chapter);
    }

    // Update an existing chapter
    public function update(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'subject_id' => 'exists:subjects,id',
            'number' => 'string|max:50',
            'name' => 'string|max:255',
        ]);

        $chapter->update($validated);
        return response()->json([
            'message' => 'Chapter updated successfully',
            'chapter' => $chapter
        ]);
    }

    // Delete a chapter
    public function destroy(Chapter $chapter)
    {
        $chapter->delete();
        return response()->json([
            'message' => 'Chapter deleted successfully'
        ]);
    }
}
