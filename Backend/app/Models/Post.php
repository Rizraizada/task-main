<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // Define fillable attributes
    protected $fillable = [
        'user_id',
        'question_id',  // Make sure to add question_id if it's used.
        'subject_id',
        'chapter_id',
        'text',
        'post_text',
        'media'
    ];

    protected $casts = [
        'media_url' => 'array',
    ];

    // Automatically manage timestamps for created_at and updated_at
    public $timestamps = true;

    // Define the relationships with other models

    // Relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with the Subject model
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Relationship with the Chapter model
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    // Relationship with the Question model
    public function question()
    {
        return $this->belongsTo(Question::class);  // Assuming Question model exists
    }

    // Relationship with the Comment model
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
