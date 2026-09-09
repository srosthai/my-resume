<?php

namespace Database\Factories;

use App\Models\AboutMe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AboutMe>
 */
class AboutMeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'About Me',
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'year_experience' => '3+ Years',
            'focus_on' => 'Backend Development',
        ];
    }
}
