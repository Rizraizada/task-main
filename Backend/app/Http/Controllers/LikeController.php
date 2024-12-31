<?php
namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function index()
    {
        return Like::with('user', 'post', 'comment')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'nullable|exists:posts,id',
            'comment_id' => 'nullable|exists:comments,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if the user already liked the post or comment
        if ($validated['post_id']) {
            $existingLike = Like::where('post_id', $validated['post_id'])
                                ->where('user_id', $validated['user_id'])
                                ->first();
        } elseif ($validated['comment_id']) {
            $existingLike = Like::where('comment_id', $validated['comment_id'])
                                ->where('user_id', $validated['user_id'])
                                ->first();
        }

        if ($existingLike) {
            return response()->json(['message' => 'You already liked this post or comment'], 400);
        }

        return Like::create($validated);
    }

    public function show(Like $like)
    {
        return $like;
    }

    public function destroy(Like $like)
    {
        $like->delete();
        return response()->json(['message' => 'Like deleted successfully']);
    }
}
