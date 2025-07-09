<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechStack extends Model
{
    use HasFactory;

    protected $table = 'tech_stacks';

    protected $fillable = [
        'name',
        'logo',
        'type',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}