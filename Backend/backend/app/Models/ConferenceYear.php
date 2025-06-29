<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceYear extends Model
{
    protected $table = 'years';

    protected $fillable = [
        'year',
    ];

    public function users()
{
    return $this->belongsToMany(User::class, 'user_year', 'year_id', 'user_id');
}

    public function subpages()
    {
        return $this->hasMany(Subpage::class, 'year_id');
    }
}
