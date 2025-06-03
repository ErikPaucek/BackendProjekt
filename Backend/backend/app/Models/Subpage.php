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
                $subpage->slug = Str::slug($subpage->title . '-' . $subpage->year_id);
            }
        });

        static::updating(function ($subpage) {
            if (empty($subpage->slug) || $subpage->isDirty('title') || $subpage->isDirty('year_id')) {
                $subpage->slug = Str::slug($subpage->title . '-' . $subpage->year_id);
            }
        });
    }

    public function conferenceYear()
    {
        return $this->belongsTo(ConferenceYear::class, 'year_id');
    }
}