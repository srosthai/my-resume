<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates a unique slug from {@see slugSource()} on create, and again on
 * update when the source changed.
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->slug)) {
                $model->slug = $model->uniqueSlug();
            }
        });

        static::updating(function (Model $model): void {
            if ($model->slugSourceChanged()) {
                $model->slug = $model->uniqueSlug();
            }
        });
    }

    /**
     * The string the slug is derived from.
     */
    abstract protected function slugSource(): string;

    /**
     * Attributes that, when dirty, cause the slug to be regenerated.
     *
     * @return list<string>
     */
    protected function slugSourceAttributes(): array
    {
        return ['title'];
    }

    protected function slugSourceChanged(): bool
    {
        return $this->isDirty($this->slugSourceAttributes());
    }

    protected function uniqueSlug(): string
    {
        $base = Str::slug($this->slugSource()) ?: Str::lower(Str::random(8));
        $slug = $base;
        $counter = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
