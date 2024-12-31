<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'user_id', 'comment_text', 'parent_comment_id', 'media_url'];

    protected $casts = [
        'media_url' => 'array', // Cast media_url as an array
    ];

    // Relationship with the post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Relationship with the user
// Relationship with the user
public function user()
{
    return $this->belongsTo(User::class);
}

// Replies to the comment
public function replies()
{
    return $this->hasMany(Comment::class, 'parent_comment_id')->with('user', 'replies');
}


    // Parent comment relationship
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }
}
