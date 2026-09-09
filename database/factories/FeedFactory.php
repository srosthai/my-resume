<?php

namespace Database\Factories;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feed>
 */
class FeedFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'images' => null,
            'location' => fake()->city(),
            'mood' => 'happy',
            'activity_type' => 'travel',
            'tags' => ['travel'],
            'visibility' => 'public',
            'status' => 'published',
            'likes_count' => 0,
            'views' => 0,
            'is_pinned' => false,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'published_at' => null]);
    }

    public function private(): static
    {
        return $this->state(fn () => ['visibility' => 'private']);
    }
}
