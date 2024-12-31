<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\User;
use App\Models\Comment;
use App\Models\Question;  // Assuming Question model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{


    public function index()
    {
        $posts = Post::with([
            'subject',
            'chapter',
            'user',
            'comments' => function ($query) {
                $query->with(['user', 'replies.user']); // Include nested replies with user details
            }
        ])->get();

        $posts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'post_text' => $post->post_text,
                'text' => $post->text,
                'image_url' => $post->image_url,
                'date' => $post->created_at,

                'voice_url' => $post->voice_url,
                'subject_code' => $post->subject ? $post->subject->code : null,
                'chapter_name' => $post->chapter ? $post->chapter->name : null,
                'question_text' => $post->question ? $post->question->text : null,
                'user_username' => $post->user ? $post->user->username : null,
                'user_profile_picture' => $post->user ? $post->user->profile_picture : null,
                'comments' => $post->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'comment_text' => $comment->comment_text,
                        'media_url' => $comment->media_url,
                        'user' => [
                            'username' => $comment->user->username,
                            'profile_picture' => $comment->user->profile_picture,
                        ],
                        'replies' => $comment->replies->map(function ($reply) {
                            return [
                                'id' => $reply->id,
                                'comment_text' => $reply->comment_text,
                                'media_url' => $reply->media_url,
                                'user' => [
                                    'username' => $reply->user->username,
                                    'profile_picture' => $reply->user->profile_picture,
                                ],
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json($posts);
    }





 public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'subject_id' => 'required|exists:subjects,id',
        'post_text' => 'required|string',
        'question_id' => 'nullable|exists:questions,id',
        'chapter_id' => 'nullable|exists:chapters,id',
        'text' => 'nullable|string',
        'media' => 'nullable|array', // Ensure media handling is correct
        'media.*' => 'file|mimes:jpg,jpeg,png,mp3,wav'
    ]);

    // Create the post first
    $post = Post::create([
        'user_id' => $validated['user_id'],
        'subject_id' => $validated['subject_id'],
        'post_text' => $validated['post_text'],
        'question_id' => $validated['question_id'] ?? null,
        'chapter_id' => $validated['chapter_id'] ?? null,
        'text' => $validated['text'] ?? null,
    ]);

    // Handle media if uploaded
    $mediaPaths = [];
    if ($request->hasFile('media')) {
        foreach ($request->file('media') as $file) {
            // Determine the file's directory based on its MIME type
            if (in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                // Store in the 'images' directory
                $filePath = $file->storeAs('public/media/images', $file->getClientOriginalName());
            } elseif (in_array($file->getClientMimeType(), ['audio/mp3', 'audio/wav'])) {
                // Store in the 'audios' directory
                $filePath = $file->storeAs('public/media/audios', $file->getClientOriginalName());
            }

            // Store the relative file path (without the 'public/' prefix)
            $mediaPaths[] = str_replace('public/', '', $filePath);
        }
    }

    // Update the post with the media URLs (relative paths)
    $post->media_url = $mediaPaths; // Store the relative paths of uploaded media
    $post->save();

    return response()->json(['post' => $post], 201);
}


    // Show a specific post with related data (user, subject, chapter, comments, and media)
    public function show($id)
    {
        $post = Post::with('subject', 'chapter', 'user', 'comments')->findOrFail($id);

        // Return the post with associated data in JSON format
        return response()->json([
            'post' => $post
        ]);
    }

    // Update an existing post
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'post_text' => 'nullable|string',
            'question_id' => 'nullable|exists:questions,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'text' => 'nullable|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp3,wav'
        ]);

        // Find the post and update it with validated data
        $post = Post::findOrFail($id);
        $post->update($validated);

        // Handle new media uploads if present
        if ($request->hasFile('media')) {
            $mediaUrls = [];
            foreach ($request->file('media') as $file) {
                // Store each file and get the file path
                $mediaUrl = $file->store('public/media');
                $mediaUrls[] = Storage::url($mediaUrl); // Store the public URL of the file
            }

            // Store media URLs in the 'media_url' field as a JSON array
            $post->media_url = $mediaUrls;
            $post->save();
        }

        // Return the updated post with media URLs in the response
        return response()->json([
            'post' => $post
        ]);
    }

    // Delete a post
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}
