<?php

namespace App\Models\Concerns;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared draft / published / archived behaviour.
 * Stamps published_at the first time a record is saved as published.
 */
trait Publishable
{
    protected static function bootPublishable(): void
    {
        static::saving(function (Model $model): void {
            if ($model->status === PublishStatus::Published && $model->published_at === null) {
                $model->published_at = now();
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published)->whereNotNull('published_at');
    }

    public function isPublished(): bool
    {
        return $this->status === PublishStatus::Published && $this->published_at !== null;
    }
}
