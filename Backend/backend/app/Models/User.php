<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

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
        return $this->belongsToMany(ConferenceYear::class, 'user_year', 'user_id', 'year_id');
    }
}
