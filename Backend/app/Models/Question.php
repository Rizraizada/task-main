<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['chapter_id', 'text', 'question_text'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
