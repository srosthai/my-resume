<?php

namespace Database\Factories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'category' => 'Laravel',
            'description' => fake()->sentence(10),
            'tags' => ['laravel'],
            'content' => [
                'overview' => fake()->paragraph(),
                'requirements' => ['PHP 8.2'],
                'steps' => [
                    ['title' => 'Install', 'description' => 'Run the installer.', 'commands' => ['composer install']],
                ],
            ],
            'status' => 'published',
            'views' => 0,
            'is_featured' => false,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'published_at' => null]);
    }
}
