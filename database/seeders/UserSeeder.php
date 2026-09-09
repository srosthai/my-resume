<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Create the single portfolio owner (the only account that can enter the admin).
     *
     * Override the credentials with SEED_OWNER_EMAIL / SEED_OWNER_PASSWORD in .env;
     * the defaults are for local development only.
     */
    public function run(): void
    {
        User::query()->firstOr(fn () => User::forceCreate([
            'name' => 'SROS THAI',
            'email' => env('SEED_OWNER_EMAIL', 'srosthai00@gmail.com'),
            'email_verified_at' => now(),
            'password' => Hash::make(env('SEED_OWNER_PASSWORD', '12345678')),
            'is_owner' => true,
            'dob' => '1995-01-15',
            'phone' => '+855 12 345 678',
            'address' => 'Phnom Penh, Cambodia',
            'position' => 'Full Stack Developer',
            'description' => 'Passionate software developer with expertise in Laravel, Vue.js, and modern web technologies. Building scalable and maintainable applications.',
            'image' => null,
        ]));
    }
}
