<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceYear extends Model
{
    protected $table = 'years'; // explicitne definujeme názov tabuľky

    protected $fillable = [
        'year',
    ];

    // Rok (ročník) môže mať viacerých používateľov (editorov)
    public function users()
{
    return $this->belongsToMany(User::class, 'user_year', 'year_id', 'user_id');
}

    // Rok má viaceré podstránky
    public function subpages()
    {
        return $this->hasMany(Subpage::class, 'year_id');
    }
}
