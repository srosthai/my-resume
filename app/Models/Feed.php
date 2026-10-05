<?php

namespace App\Models;

use App\Enums\FeedVisibility;
use App\Enums\PublishStatus;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Feed extends Model
{
    use HasFactory, HasSlug, Publishable, SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'images',
        'location',
        'mood',
        'activity_type',
        'tags',
        'visibility',
        'status',
        'likes_count',
        'is_pinned',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'tags' => 'array',
            'is_pinned' => 'boolean',
            'status' => PublishStatus::class,
            'visibility' => FeedVisibility::class,
            'published_at' => 'datetime',
        ];
    }

    protected function slugSource(): string
    {
        return $this->title ?: Str::limit((string) $this->body, 50, '');
    }

    protected function slugSourceAttributes(): array
    {
        return ['title'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visibility', FeedVisibility::Public);
    }

    public function scopeActivityType(Builder $query, string $type): Builder
    {
        return $query->where('activity_type', $type);
    }

    protected function tagsString(): Attribute
    {
        return Attribute::get(fn () => is_array($this->tags) ? implode(', ', $this->tags) : '');
    }

    /**
     * @return Collection<int, string>
     */
    public static function getActivityTypes(): Collection
    {
        return static::query()
            ->whereNotNull('activity_type')
            ->distinct()
            ->orderBy('activity_type')
            ->pluck('activity_type');
    }
}
