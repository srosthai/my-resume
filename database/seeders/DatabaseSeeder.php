<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AboutMeSeeder::class,
            WorkExperienceSeeder::class,
            EducationSeeder::class,
            TechStackSeeder::class,
            ProjectTypeSeeder::class,
            ProjectSeeder::class,
            PopularSongSeeder::class,
            NoteSeeder::class,
        ]);
    }
}
