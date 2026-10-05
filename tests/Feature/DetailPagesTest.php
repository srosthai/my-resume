<?php

use App\Models\Feed;
use App\Models\Note;
use App\Models\Project;
use Inertia\Testing\AssertableInertia;

test('a published note has its own page with related notes, view counting and json-ld', function () {
    $note = Note::factory()->create(['title' => 'Install Laravel', 'category' => 'Laravel']);
    Note::factory()->count(2)->create(['category' => 'Laravel']);
    Note::factory()->create(['category' => 'Vue']);

    $response = $this->get('/note/install-laravel')->assertOk()->assertInertia(fn (AssertableInertia $p) => $p
        ->component('frontend/NoteShow')
        ->where('note.slug', 'install-laravel')
        ->has('related', 2)
        ->where('jsonLd.@type', 'TechArticle'));

    $response->assertSee('application/ld+json', false)->assertSee('TechArticle', false);
    expect($note->fresh()->views)->toBe(1);

    $this->get('/note/install-laravel');
    expect($note->fresh()->views)->toBe(1); // same visitor within the cooldown
});

test('draft notes and private or draft feeds are not reachable by slug', function () {
    Note::factory()->draft()->create(['title' => 'Secret note']);
    Feed::factory()->private()->create(['title' => 'Private feed']);
    Feed::factory()->draft()->create(['title' => 'Draft feed']);

    $this->get('/note/secret-note')->assertNotFound();
    $this->get('/feeds/private-feed')->assertNotFound();
    $this->get('/feeds/draft-feed')->assertNotFound();
    $this->get('/note/does-not-exist')->assertNotFound();
});

test('a public feed has its own page with absolute image urls', function () {
    $feed = Feed::factory()->create(['title' => 'Weekend Hangout', 'images' => ['uploads/feeds/a.jpg']]);

    $this->get('/feeds/weekend-hangout')->assertOk()->assertInertia(fn (AssertableInertia $p) => $p
        ->component('frontend/FeedShow')
        ->where('feed.images.0', url('uploads/feeds/a.jpg'))
        ->where('jsonLd.@type', 'SocialMediaPosting'));

    expect($feed->fresh()->views)->toBe(1);
});

test('project neighbours follow portfolio order rather than insert order', function () {
    Project::factory()->create(['title' => 'Older work', 'created_date' => '2020-01-01']);
    Project::factory()->create(['title' => 'Newer work', 'created_date' => '2024-06-01']);
    Project::factory()->create(['title' => 'Middle work', 'created_date' => '2022-03-01']);

    $this->get('/portfolio/middle-work')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->where('previousProject.title', 'Newer work')
        ->where('nextProject.title', 'Older work'));

    $this->get('/portfolio/newer-work')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->where('previousProject', null)
        ->where('nextProject.title', 'Middle work'));
});

test('projects resolve by slug and old numeric urls redirect permanently', function () {
    $project = Project::factory()->create(['title' => 'Shop Platform']);
    expect($project->slug)->toBe('shop-platform');

    $this->get('/portfolio/shop-platform')->assertOk()->assertInertia(fn (AssertableInertia $p) => $p
        ->component('frontend/ProjectDetail')->where('project.slug', 'shop-platform'));

    $this->get('/portfolio/'.$project->id)->assertRedirect('/portfolio/shop-platform')->assertStatus(301);
    $this->get('/portfolio/nope')->assertNotFound();
});

test('the sitemap lists project, note and feed permalinks', function () {
    Project::factory()->create(['title' => 'Shop Platform']);
    Note::factory()->create(['title' => 'Install Laravel']);
    Note::factory()->draft()->create(['title' => 'Hidden']);
    Feed::factory()->create(['title' => 'Weekend']);

    $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

    expect($xml)->toContain('/portfolio/shop-platform')
        ->toContain('/note/install-laravel')
        ->toContain('/feeds/weekend')
        ->not->toContain('/note/hidden');
});
