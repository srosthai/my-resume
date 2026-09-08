<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopularSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'url',
        'duration',
    ];

    protected $appends = ['formatted_duration'];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
        ];
    }

    /**
     * Duration as m:ss.
     */
    protected function formattedDuration(): Attribute
    {
        return Attribute::get(fn () => sprintf('%d:%02d', intdiv((int) $this->duration, 60), ((int) $this->duration) % 60));
    }
}
