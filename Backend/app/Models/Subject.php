<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['teacher', 'code'];

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
}

