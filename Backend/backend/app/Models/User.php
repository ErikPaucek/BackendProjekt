<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    // User môže byť priradený k viacerým ročníkom konferencie
    public function years()
    {
        return $this->belongsToMany(Year::class, 'user_year', 'user_id', 'year_id');
    }
}
