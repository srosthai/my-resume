<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'description',
        'tags',
        'content',
        'slug',
        'status',
        'user_id',
        'views',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'content' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($note) {
            if (empty($note->slug)) {
                $note->slug   = Str::slug($note->title);
                $originalSlug = $note->slug;
                $counter      = 1;
                while (static::where('slug', $note->slug)->exists()) {
                    $note->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        static::updating(function ($note) {
            if ($note->isDirty('title')) {
                $note->slug   = Str::slug($note->title);
                $originalSlug = $note->slug;
                $counter      = 1;
                while (static::where('slug', $note->slug)->where('id', '!=', $note->id)->exists()) {
                    $note->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
    }

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include published notes.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }

    /**
     * Scope a query to only include featured notes.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Increment views count.
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Get formatted tags as string.
     */
    public function getTagsStringAttribute()
    {
        return is_array($this->tags) ? implode(', ', $this->tags) : '';
    }

    /**
     * Get all unique categories.
     */
    public static function getCategories()
    {
        return static::distinct('category')
                    ->whereNotNull('category')
                    ->pluck('category')
                    ->sort()
                    ->values();
    }
}