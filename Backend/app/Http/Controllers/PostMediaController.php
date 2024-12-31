<?php
namespace App\Http\Controllers;

use App\Models\PostMedia;
use Illuminate\Http\Request;

class PostMediaController extends Controller
{
    public function index()
    {
        return PostMedia::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'media_type' => 'required|string|max:50',
        ]);

        return PostMedia::create($validated);
    }

    public function show(PostMedia $postMedia)
    {
        return $postMedia;
    }

    public function update(Request $request, PostMedia $postMedia)
    {
        $validated = $request->validate([
            'post_id' => 'exists:posts,id',
            'media_type' => 'string|max:50',
        ]);

        $postMedia->update($validated);
        return $postMedia;
    }

    public function destroy(PostMedia $postMedia)
    {
        $postMedia->delete();
        return response()->json(['message' => 'Post Media deleted successfully']);
    }
}
