<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function getPostComments($postId)
    {
        // Fetch comments for the given post ID
        $comments = Comment::with(['user', 'replies.user']) // Include user and nested replies with user data
            ->where('post_id', $postId)
            ->whereNull('parent_comment_id') // Fetch only top-level comments
            ->get();

        if ($comments->isEmpty()) {
            return response()->json(['message' => 'No comments found for this post'], 404);
        }

        return response()->json($comments, 200);
    }




    // Store a comment or reply
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'comment_text' => 'required|string',
            'parent_comment_id' => 'nullable|exists:comments,id', // Null for top-level comments
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp3,wav',
        ]);

        // Handle media files
        $mediaUrls = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $mediaPath = $file->store('public/media');
                $mediaUrls[] = Storage::url($mediaPath);
            }
        }

        // Create the comment or reply
        $comment = Comment::create(array_merge($validated, ['media_url' => $mediaUrls]));

        return response()->json($comment, 201);
    }

    // Show a specific comment with its replies
    public function show($id)
    {
        $comment = Comment::with('user', 'replies')->findOrFail($id);

        return response()->json($comment);
    }

    // Update a comment or reply
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comment_text' => 'nullable|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp3,wav',
        ]);

        $comment = Comment::findOrFail($id);

        // Handle media files
        if ($request->hasFile('media')) {
            $mediaUrls = [];
            foreach ($request->file('media') as $file) {
                $mediaPath = $file->store('public/media');
                $mediaUrls[] = Storage::url($mediaPath);
            }
            $validated['media_url'] = $mediaUrls;
        }

        $comment->update($validated);

        return response()->json($comment);
    }

    // Delete a comment or reply
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
