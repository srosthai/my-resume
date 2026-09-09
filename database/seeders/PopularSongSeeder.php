<?php

namespace Database\Seeders;

use App\Models\PopularSong;
use Illuminate\Database\Seeder;

class PopularSongSeeder extends Seeder
{
    /**
     * The public music player only plays YouTube links, so seed real ones.
     */
    public function run(): void
    {
        $songs = [
            ['title' => 'បងក្រ', 'artist' => 'Tena feat. YCN Rakhie', 'url' => 'https://www.youtube.com/watch?v=-IQcA1jmb3I', 'duration' => 250],
            ['title' => '360', 'artist' => 'VannDa', 'url' => 'https://www.youtube.com/watch?v=VangtodgL0Y', 'duration' => 221],
            ['title' => 'យប់ស្ងាត់ (Quiet Night)', 'artist' => 'TEPPISETH', 'url' => 'https://www.youtube.com/watch?v=JLevKPoa6BI', 'duration' => 146],
            ['title' => 'រៀនចប់', 'artist' => 'All3rgy & Chan Sreykhouch', 'url' => 'https://www.youtube.com/watch?v=8oLi5b4w4PQ', 'duration' => 227],
        ];

        foreach ($songs as $song) {
            PopularSong::firstOrCreate(['url' => $song['url']], $song);
        }
    }
}
