<?php

namespace App\Models;

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

class Note extends Model
{
    use HasFactory, HasSlug, Publishable, SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'description',
        'tags',
        'content',
        'status',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'content' => 'array',
            'is_featured' => 'boolean',
            'status' => PublishStatus::class,
            'published_at' => 'datetime',
        ];
    }

    protected function slugSource(): string
    {
        return (string) $this->title;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    protected function tagsString(): Attribute
    {
        return Attribute::get(fn () => is_array($this->tags) ? implode(', ', $this->tags) : '');
    }

    /**
     * @return Collection<int, string>
     */
    public static function getCategories(): Collection
    {
        return static::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }
}
