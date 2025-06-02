<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subpage extends Model
{
    protected $fillable = [
        'year_id',
        'title',
        'content',
    ];
    public function conferenceYear()
    {
        return $this->belongsTo(ConferenceYear::class, 'year_id');
    }
}
