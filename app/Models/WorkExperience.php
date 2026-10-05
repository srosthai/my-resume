<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $table = 'work_experiences';

    protected $fillable = [
        'title',
        'position',
        'company',
        'description',
        'from',
        'to',
    ];

    protected function casts(): array
    {
        return [
            'from' => 'integer',
            'to' => 'integer',
        ];
    }

    /**
     * Newest start year first. A missing start year sorts last on every driver.
     */
    public function scopeInCareerOrder(Builder $query): Builder
    {
        return $query
            ->orderByRaw('case when "from" is null then 1 else 0 end')
            ->orderByDesc('from')
            ->orderByDesc('id');
    }
}
