<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'access_level',
        'start_at',
        'end_at',
        'prize_details',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'prize_details' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }
}
