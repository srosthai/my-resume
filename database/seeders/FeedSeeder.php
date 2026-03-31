<?php

namespace Database\Seeders;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Database\Seeder;

class FeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $feeds = [
            [
                'title'         => 'Weekend Hangout with Friends',
                'body'          => "Had an amazing time catching up with old friends at the local cafe! We talked about everything from tech to travel plans. Nothing beats good coffee and great company on a lazy Saturday afternoon.",
                'location'      => 'Starbucks, Phnom Penh',
                'mood'          => 'happy',
                'activity_type' => 'hangout',
                'tags'          => ['friends', 'coffee', 'weekend'],
                'visibility'    => 'public',
                'status'        => 'published',
                'likes_count'   => 42,
                'views'         => 156,
                'is_pinned'     => false,
                'published_at'  => now()->subDays(1),
            ],
            [
                'title'         => 'Exploring Siem Reap',
                'body'          => "Finally visited Angkor Wat! The sunrise view was absolutely breathtaking. Spent the whole day exploring different temples and learning about Khmer history. This is definitely a must-visit destination for everyone.",
                'location'      => 'Angkor Wat, Siem Reap',
                'mood'          => 'adventurous',
                'activity_type' => 'travel',
                'tags'          => ['travel', 'angkorwat', 'cambodia', 'history'],
                'visibility'    => 'public',
                'status'        => 'published',
                'likes_count'   => 128,
                'views'         => 342,
                'is_pinned'     => true,
                'published_at'  => now()->subDays(3),
            ],
            [
                'title'         => 'Best Ramen in Town',
                'body'          => "Discovered this hidden gem ramen shop tucked away in a small alley. The tonkotsu broth was rich and flavorful, and the chashu pork was melt-in-your-mouth tender. Definitely coming back for more!",
                'location'      => 'BKK1, Phnom Penh',
                'mood'          => 'excited',
                'activity_type' => 'food',
                'tags'          => ['food', 'ramen', 'japanese', 'foodie'],
                'visibility'    => 'public',
                'status'        => 'published',
                'likes_count'   => 67,
                'views'         => 203,
                'is_pinned'     => false,
                'published_at'  => now()->subDays(5),
            ],
            [
                'title'         => 'Morning Routine',
                'body'          => "Started a new morning routine: wake up at 5:30 AM, 30 minutes of exercise, cold shower, and then a healthy breakfast before coding. It's been two weeks and I already feel more productive and energetic throughout the day.",
                'location'      => null,
                'mood'          => 'energetic',
                'activity_type' => 'lifestyle',
                'tags'          => ['routine', 'productivity', 'health', 'motivation'],
                'visibility'    => 'public',
                'status'        => 'published',
                'likes_count'   => 89,
                'views'         => 267,
                'is_pinned'     => false,
                'published_at'  => now()->subDays(7),
            ],
            [
                'title'         => 'Project Launch Day!',
                'body'          => "After months of hard work, we finally launched the new platform! The team put in incredible effort and I'm so proud of what we've built together. Grateful for such an amazing team. Here's to many more successful launches!",
                'location'      => null,
                'mood'          => 'grateful',
                'activity_type' => 'work',
                'tags'          => ['launch', 'teamwork', 'coding', 'milestone'],
                'visibility'    => 'public',
                'status'        => 'published',
                'likes_count'   => 156,
                'views'         => 445,
                'is_pinned'     => true,
                'published_at'  => now()->subDays(10),
            ],
            [
                'title'         => 'Tech Meetup Night',
                'body'          => "Attended the monthly tech meetup at the innovation hub. Great talks on AI, blockchain, and web development. Met some really inspiring developers and exchanged ideas. The tech community here is growing fast!",
                'location'      => 'Factory Phnom Penh',
                'mood'          => 'excited',
                'activity_type' => 'event',
                'tags'          => ['tech', 'meetup', 'networking', 'community'],
                'visibility'    => 'public',
                'status'        => 'published',
                'likes_count'   => 73,
                'views'         => 198,
                'is_pinned'     => false,
                'published_at'  => now()->subDays(14),
            ],
        ];

        foreach ($feeds as $feed) {
            Feed::create(array_merge($feed, [
                'user_id' => $user->id ?? null,
            ]));
        }
    }
}
