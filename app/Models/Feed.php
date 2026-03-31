<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'images',
        'location',
        'mood',
        'activity_type',
        'tags',
        'slug',
        'visibility',
        'status',
        'user_id',
        'likes_count',
        'views',
        'is_pinned',
        'published_at',
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feed) {
            if (empty($feed->slug)) {
                $source       = $feed->title ?: Str::limit($feed->body, 50, '');
                $feed->slug   = Str::slug($source);
                $originalSlug = $feed->slug;
                $counter      = 1;
                while (static::where('slug', $feed->slug)->exists()) {
                    $feed->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        static::updating(function ($feed) {
            if ($feed->isDirty('title')) {
                $source       = $feed->title ?: Str::limit($feed->body, 50, '');
                $feed->slug   = Str::slug($source);
                $originalSlug = $feed->slug;
                $counter      = 1;
                while (static::where('slug', $feed->slug)->where('id', '!=', $feed->id)->exists()) {
                    $feed->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeActivityType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getTagsStringAttribute()
    {
        return is_array($this->tags) ? implode(', ', $this->tags) : '';
    }

    public static function getActivityTypes()
    {
        return static::distinct('activity_type')
                    ->whereNotNull('activity_type')
                    ->pluck('activity_type')
                    ->sort()
                    ->values();
    }
}
