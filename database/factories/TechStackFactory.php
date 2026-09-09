<?php

namespace Database\Factories;

use App\Models\TechStack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TechStack>
 */
class TechStackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'logo' => 'https://cdn.example.com/logo.svg',
            'type' => 'Backend',
            'description' => fake()->sentence(),
        ];
    }
}
