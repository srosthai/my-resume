<?php

namespace App\Models;

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

    protected $casts = [
        'duration' => 'integer',
    ];

    /**
     * Get the formatted duration in minutes:seconds
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Scope to get only active songs (if needed for future features)
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('url');
    }

    /**
     * Get the file extension from URL
     */
    public function getFileExtensionAttribute(): string
    {
        $path = parse_url($this->url, PHP_URL_PATH);
        return pathinfo($path, PATHINFO_EXTENSION) ?: 'mp3';
    }
}