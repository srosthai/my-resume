<?php

use App\Models\Feed;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    $this->owner = User::factory()->owner()->create();
});

function feedPayload(array $overrides = []): array
{
    return array_merge([
        'body' => 'Feed body text',
        'visibility' => 'public',
        'status' => 'published',
    ], $overrides);
}

test('uploaded feed images are stored on the uploads disk under uploads/feeds', function () {
    $this->actingAs($this->owner)
        ->post(route('backend.feeds.store'), feedPayload([
            'images' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')],
        ]))
        ->assertRedirect(route('backend.feeds.index'));

    $feed = Feed::first();
    expect($feed->images)->toHaveCount(2);
    foreach ($feed->images as $path) {
        expect($path)->toStartWith('uploads/feeds/');
        Storage::disk('uploads')->assertExists(substr($path, strlen('uploads/')));
    }
});

test('existing_images cannot inject foreign paths or delete files outside the feed', function () {
    Storage::disk('uploads')->put('feeds/keep.jpg', 'x');
    Storage::disk('uploads')->put('feeds/drop.jpg', 'x');
    Storage::disk('uploads')->put('projects/other.jpg', 'x');

    $feed = Feed::factory()->create(['images' => ['uploads/feeds/keep.jpg', 'uploads/feeds/drop.jpg']]);

    $this->actingAs($this->owner)
        ->put(route('backend.feeds.update', $feed), feedPayload([
            'existing_images' => [
                'uploads/feeds/keep.jpg',
                '../../.env',
                'uploads/projects/other.jpg',
                'https://evil.example/x.jpg',
            ],
        ]))
        ->assertRedirect(route('backend.feeds.index'));

    expect($feed->fresh()->images)->toBe(['uploads/feeds/keep.jpg']);
    Storage::disk('uploads')->assertExists('feeds/keep.jpg');
    Storage::disk('uploads')->assertMissing('feeds/drop.jpg');
    Storage::disk('uploads')->assertExists('projects/other.jpg');
    expect(file_exists(base_path('.env')))->toBeTrue();
});

test('deleting a feed removes only its own images', function () {
    Storage::disk('uploads')->put('feeds/mine.jpg', 'x');
    Storage::disk('uploads')->put('feeds/theirs.jpg', 'x');

    $feed = Feed::factory()->create(['images' => ['uploads/feeds/mine.jpg']]);
    Feed::factory()->create(['images' => ['uploads/feeds/theirs.jpg']]);

    $this->actingAs($this->owner)->delete(route('backend.feeds.destroy', $feed))->assertRedirect();

    Storage::disk('uploads')->assertMissing('feeds/mine.jpg');
    Storage::disk('uploads')->assertExists('feeds/theirs.jpg');
});

test('a stored path pointing outside uploads is never unlinked', function () {
    $feed = Feed::factory()->create(['images' => ['../index.php', '/etc/passwd', 'uploads/../index.php']]);

    $this->actingAs($this->owner)->delete(route('backend.feeds.destroy', $feed))->assertRedirect();

    expect(file_exists(public_path('index.php')))->toBeTrue();
});
