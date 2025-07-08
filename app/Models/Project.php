<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

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
        'links'        => 'array',
        'created_date' => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Get the project type associated with this project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }
}