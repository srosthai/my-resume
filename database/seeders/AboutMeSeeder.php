<?php

namespace Database\Seeders;

use App\Models\AboutMe;
use Illuminate\Database\Seeder;

class AboutMeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AboutMe::create([
            'title' => 'SROS THAI',
            'description' => 'I am a passionate Full Stack Developer with a strong focus on building modern, scalable web applications. With expertise in Laravel, Vue.js, and cloud technologies, I create elegant solutions that solve real-world problems. I believe in writing clean, maintainable code and continuously learning new technologies to stay at the forefront of web development.',
            'location' => 'Phnom Penh, Cambodia',
            'year_experience' => '5+ Years',
            'fucus_on' => 'Web Development',
        ]);
    }
}
