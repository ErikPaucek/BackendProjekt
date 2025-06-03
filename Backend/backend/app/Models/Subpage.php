<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subpage extends Model
{
    protected $fillable = [
        'year_id',
        'title',
        'slug',
        'content',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($subpage) {
            if (empty($subpage->slug)) {
                $year = \App\Models\ConferenceYear::find($subpage->year_id);
                $subpage->slug = Str::slug($subpage->title . '-' . ($year ? $year->year : ''));
            }
        });

        static::updating(function ($subpage) {
            if (empty($subpage->slug) || $subpage->isDirty('title') || $subpage->isDirty('year_id')) {
                $year = \App\Models\ConferenceYear::find($subpage->year_id);
                $subpage->slug = Str::slug($subpage->title . '-' . ($year ? $year->year : ''));
            }
        });
    }

    public function conferenceYear()
    {
        return $this->belongsTo(\App\Models\ConferenceYear::class, 'year_id');
    }
}