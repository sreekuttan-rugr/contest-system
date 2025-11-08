<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contest_id',
        'status',
        'answers',
        'score',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }
}
