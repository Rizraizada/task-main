<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        // Return all subjects with their chapters in JSON format
        return response()->json(Subject::with('chapters')->get());
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'teacher' => 'required|string|max:255',
            'code' => 'required|string|max:50',
        ]);

        // Create a new subject and return it as JSON
        $subject = Subject::create($validated);
        return response()->json($subject, 201); // 201 is the HTTP status for 'Created'
    }

    public function show(Subject $subject)
    {
        // Return a specific subject with its chapters in JSON format
        return response()->json($subject->load('chapters'));
    }

    public function update(Request $request, Subject $subject)
    {
        // Validate the request data
        $validated = $request->validate([
            'teacher' => 'nullable|string|max:255',  // Making 'teacher' and 'code' nullable
            'code' => 'nullable|string|max:50',
        ]);

        // Update the subject
        $subject->update($validated);

        // Return the updated subject as JSON
        return response()->json($subject);
    }

    public function destroy(Subject $subject)
    {
        // Delete the subject
        $subject->delete();

        // Return a success message as JSON
        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
