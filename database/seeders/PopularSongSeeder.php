<?php

namespace Database\Seeders;

use App\Models\PopularSong;
use Illuminate\Database\Seeder;

class PopularSongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $songs = [
            [
                'title' => 'Lofi Coding Beats',
                'artist' => 'ChilledCow',
                'url' => 'https://example.com/songs/lofi-coding.mp3',
                'duration' => 180,
            ],
            [
                'title' => 'Focus Flow',
                'artist' => 'Productivity Music',
                'url' => 'https://example.com/songs/focus-flow.mp3',
                'duration' => 240,
            ],
            [
                'title' => 'Deep Work Ambience',
                'artist' => 'Ambient Sounds',
                'url' => 'https://example.com/songs/deep-work.mp3',
                'duration' => 300,
            ],
            [
                'title' => 'Coding in the Rain',
                'artist' => 'Nature Beats',
                'url' => 'https://example.com/songs/coding-rain.mp3',
                'duration' => 210,
            ],
            [
                'title' => 'Synthwave Dreams',
                'artist' => 'Retro Wave',
                'url' => 'https://example.com/songs/synthwave.mp3',
                'duration' => 195,
            ],
        ];

        foreach ($songs as $song) {
            PopularSong::create($song);
        }
    }
}
