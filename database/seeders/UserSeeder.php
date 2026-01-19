<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'SROS THAI',
            'email' => 'srosthai00@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'dob' => '1995-01-15',
            'phone' => '+855 12 345 678',
            'address' => 'Phnom Penh, Cambodia',
            'position' => 'Full Stack Developer',
            'description' => 'Passionate software developer with expertise in Laravel, Vue.js, and modern web technologies. Building scalable and maintainable applications.',
            'image' => null,
        ]);
    }
}
