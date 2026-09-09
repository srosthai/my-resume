<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'phone',
        'address',
        'position',
        'description',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'is_owner' => 'boolean',
        ];
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function feeds(): HasMany
    {
        return $this->hasMany(Feed::class);
    }

    /**
     * Scope a query to the single portfolio owner.
     */
    public function scopeOwner(Builder $query): Builder
    {
        return $query->where('is_owner', true);
    }

    /**
     * Columns that are safe to expose on public pages.
     *
     * @return list<string>
     */
    public static function publicColumns(): array
    {
        return ['id', 'name', 'position', 'description', 'image'];
    }
}
