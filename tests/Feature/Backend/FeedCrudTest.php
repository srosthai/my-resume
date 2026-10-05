<?php

use App\Models\Feed;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(fn () => $this->owner = User::factory()->owner()->create());

test('a feed without a title derives its slug from the body and belongs to the current user', function () {
    $this->actingAs($this->owner)->post(route('backend.feeds.store'), [
        'body' => 'Had an amazing time catching up with old friends at the local cafe today',
        'visibility' => 'public',
        'status' => 'published',
        'tags' => ['friends', ''],
    ])->assertRedirect(route('backend.feeds.index'));

    $feed = Feed::first();
    expect($feed->user_id)->toBe($this->owner->id)
        ->and($feed->slug)->toBe('had-an-amazing-time-catching-up-with-old-friends-a')
        ->and($feed->tags)->toBe(['friends'])
        ->and($feed->published_at)->not->toBeNull();
});

test('invalid visibility or status is rejected', function () {
    $this->actingAs($this->owner)
        ->post(route('backend.feeds.store'), ['body' => 'x', 'visibility' => 'friends', 'status' => 'live'])
        ->assertSessionHasErrors(['visibility', 'status']);
});

test('pin toggle, update and destroy', function () {
    $feed = Feed::factory()->create(['user_id' => $this->owner->id]);
    $this->actingAs($this->owner);

    $this->patch(route('backend.feeds.toggle-pinned', $feed))->assertRedirect();
    expect($feed->fresh()->is_pinned)->toBeTrue();

    $this->put(route('backend.feeds.update', $feed), ['body' => 'Edited', 'visibility' => 'private', 'status' => 'published'])->assertRedirect();
    expect($feed->fresh()->body)->toBe('Edited');

    $this->delete(route('backend.feeds.destroy', $feed))->assertRedirect();
    expect(Feed::count())->toBe(0);
});

test('private and draft feeds never reach the public page, pinned ones come first', function () {
    Feed::factory()->create(['title' => 'Old', 'published_at' => now()->subDays(2)]);
    Feed::factory()->create(['title' => 'Pinned', 'is_pinned' => true, 'published_at' => now()->subDays(5)]);
    Feed::factory()->private()->create(['title' => 'Private']);
    Feed::factory()->draft()->create(['title' => 'Draft']);

    $this->get('/feeds')->assertOk()->assertInertia(fn (AssertableInertia $p) => $p->has('feeds', 2)->where('feeds.0.title', 'Pinned')->where('feeds.1.title', 'Old'));
});

test('the feeds index does not count views; opening a feed does, once per cooldown', function () {
    $feed = Feed::factory()->create(['title' => 'Weekend market', 'views' => 4]);

    $this->get('/feeds')->assertOk();
    expect($feed->fresh()->views)->toBe(4);

    $this->get('/feeds/weekend-market')->assertOk();
    expect($feed->fresh()->views)->toBe(5);

    $this->get('/feeds/weekend-market')->assertOk();
    expect($feed->fresh()->views)->toBe(5);
});

test('like and view endpoints ignore drafts and private feeds', function () {
    $draft = Feed::factory()->draft()->create(['likes_count' => 3, 'views' => 2]);
    $private = Feed::factory()->private()->create(['likes_count' => 3, 'views' => 2]);

    $this->post("/api/feeds/{$draft->id}/like")->assertNotFound();
    $this->post("/api/feeds/{$draft->id}/view")->assertNotFound();
    $this->post("/api/feeds/{$private->id}/like")->assertNotFound();
    $this->post("/api/feeds/{$private->id}/view")->assertNotFound();

    expect($draft->fresh()->likes_count)->toBe(3)
        ->and($draft->fresh()->views)->toBe(2)
        ->and($private->fresh()->likes_count)->toBe(3)
        ->and($private->fresh()->views)->toBe(2);
});

test('unliking a feed never drives the count below zero', function () {
    $feed = Feed::factory()->create(['likes_count' => 0]);
    cache()->put('feed_like_'.$feed->id.'_127.0.0.1', true, 60);

    $this->post("/api/feeds/{$feed->id}/like")->assertOk()->assertJson(['likes_count' => 0, 'liked' => false]);

    expect($feed->fresh()->likes_count)->toBe(0);
});

test('the heart state comes from the server, not from the browser', function () {
    $newer = Feed::factory()->create(['title' => 'Liked one', 'published_at' => now()]);
    $older = Feed::factory()->create(['title' => 'Plain one', 'published_at' => now()->subDay()]);
    cache()->put('feed_like_'.$newer->id.'_127.0.0.1', true, 60);

    $this->get('/feeds')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->where('feeds.0.liked', true)
        ->where('feeds.1.liked', false));

    $this->get('/feeds/'.$newer->slug)->assertOk()->assertInertia(fn (AssertableInertia $page) => $page->where('feed.liked', true));
    $this->get('/feeds/'.$older->slug)->assertOk()->assertInertia(fn (AssertableInertia $page) => $page->where('feed.liked', false));
});

test('view and like counters are de-duplicated per visitor', function () {
    $feed = Feed::factory()->create();

    $this->post("/api/feeds/{$feed->id}/view")->assertOk()->assertJson(['views' => 1]);
    $this->post("/api/feeds/{$feed->id}/view")->assertOk()->assertJson(['views' => 1]);

    $this->post("/api/feeds/{$feed->id}/like")->assertOk()->assertJson(['likes_count' => 1, 'liked' => true]);
    $this->post("/api/feeds/{$feed->id}/like")->assertOk()->assertJson(['likes_count' => 0, 'liked' => false]);
});
