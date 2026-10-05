<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'title',
        'description',
        'image',
        'project_type_id',
        'technologies',
        'created_date',
        'status',
        'links',
    ];

    protected $casts = [
        'technologies' => 'array',
        'links' => 'array',
        'created_date' => 'date',
        'status' => ProjectStatus::class,
    ];

    /**
     * Get the project type associated with this project.
     *
     * @return BelongsTo
     */
    protected function slugSource(): string
    {
        return (string) $this->title;
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }
}
