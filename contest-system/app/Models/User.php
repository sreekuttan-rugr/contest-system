<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    public function contests()
    {
        return $this->hasMany(Contest::class, 'created_by');
    }
}
