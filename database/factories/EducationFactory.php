<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'Bachelor of IT',
            'major' => 'Software Engineering',
            'institution' => fake()->company(),
            'description' => fake()->paragraph(),
            'from' => '2018',
            'to' => '2022',
        ];
    }
}
