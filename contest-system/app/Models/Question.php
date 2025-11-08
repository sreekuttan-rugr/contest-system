<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_id',
        'type',
        'question_text',
        'options',
        'correct_answers',
        'marks',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }
}
