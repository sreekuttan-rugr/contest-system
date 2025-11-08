<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_id',
        'user_id',
        'details',
        'awarded_at',
    ];

    protected $casts = [
        'details' => 'array',
        'awarded_at' => 'datetime',
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
