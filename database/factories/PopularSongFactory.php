<?php

namespace Database\Factories;

use App\Models\PopularSong;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PopularSong>
 */
class PopularSongFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2),
            'artist' => fake()->name(),
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => 240,
        ];
    }
}
