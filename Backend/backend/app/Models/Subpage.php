<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subpage extends Model
{
    protected $fillable = [
        'conference_year_id',
        'title',
        'content',
    ];

    // Podstránka patrí konkrétnemu ročníku
    public function conferenceYear()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }

    // Podstránka má prílohy
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
