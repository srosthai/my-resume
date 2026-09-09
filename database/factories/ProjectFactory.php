<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'image' => null,
            'project_type_id' => ProjectType::factory(),
            'technologies' => ['Laravel', 'Vue'],
            'created_date' => fake()->date(),
            'status' => 'completed',
            'links' => [['Github' => 'https://github.com/example/repo']],
        ];
    }
}
