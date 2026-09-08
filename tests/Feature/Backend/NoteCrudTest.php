<?php

use App\Enums\PublishStatus;
use App\Models\Note;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(fn () => $this->owner = User::factory()->owner()->create());

function notePayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Install Laravel',
        'category' => 'Laravel',
        'description' => 'How to install.',
        'tags' => ['laravel', '', '  '],
        'content' => [
            'overview' => 'Overview',
            'requirements' => ['PHP 8.2'],
            'steps' => [['title' => 'Run', 'description' => 'Run it', 'commands' => ['composer install']]],
        ],
        'status' => 'published',
        'is_featured' => false,
    ], $overrides);
}

test('a note is created for the current user with a slug, cleaned tags and a publish timestamp', function () {
    $this->actingAs($this->owner)->post(route('backend.notes.store'), notePayload())->assertRedirect(route('backend.notes.index'));

    $note = Note::first();
    expect($note->user_id)->toBe($this->owner->id)
        ->and($note->slug)->toBe('install-laravel')
        ->and($note->tags)->toBe(['laravel'])
        ->and($note->status)->toBe(PublishStatus::Published)
        ->and($note->published_at)->not->toBeNull();
});

test('a draft has no publish timestamp until it is published, and keeps it afterwards', function () {
    $this->actingAs($this->owner)->post(route('backend.notes.store'), notePayload(['status' => 'draft']));
    $note = Note::first();
    expect($note->published_at)->toBeNull();

    $this->actingAs($this->owner)->patch(route('backend.notes.update', $note), notePayload(['status' => 'published']));
    $first = $note->fresh()->published_at;
    expect($first)->not->toBeNull();

    $this->travel(1)->day();
    $this->actingAs($this->owner)->patch(route('backend.notes.update', $note), notePayload(['status' => 'published', 'title' => 'Renamed']));
    expect($note->fresh()->published_at->equalTo($first))->toBeTrue()
        ->and($note->fresh()->slug)->toBe('renamed');
});

test('slugs are unique and duplicates get a numeric suffix', function () {
    $this->actingAs($this->owner);
    $this->post(route('backend.notes.store'), notePayload());
    $this->post(route('backend.notes.store'), notePayload());

    expect(Note::pluck('slug')->all())->toBe(['install-laravel', 'install-laravel-1']);
});

test('nested content is validated', function () {
    $this->actingAs($this->owner)
        ->post(route('backend.notes.store'), notePayload(['content' => ['overview' => 'x', 'requirements' => [], 'steps' => [['title' => '', 'description' => 'd', 'commands' => []]]]]))
        ->assertSessionHasErrors(['content.requirements', 'content.steps.0.title', 'content.steps.0.commands']);
});

test('toggle featured, duplicate and destroy work', function () {
    $note = Note::factory()->create(['user_id' => $this->owner->id, 'is_featured' => false, 'views' => 12]);
    $this->actingAs($this->owner);

    $this->patch(route('backend.notes.toggle-featured', $note))->assertRedirect();
    expect($note->fresh()->is_featured)->toBeTrue();

    $this->post(route('backend.notes.duplicate', $note))->assertRedirect();
    $copy = Note::where('id', '!=', $note->id)->first();
    expect($copy->title)->toBe($note->title.' (Copy)')
        ->and($copy->slug)->not->toBe($note->slug)
        ->and($copy->status)->toBe(PublishStatus::Draft)
        ->and($copy->views)->toBe(0)
        ->and($copy->is_featured)->toBeFalse();

    $this->delete(route('backend.notes.destroy', $note))->assertRedirect(route('backend.notes.index'));
    expect(Note::count())->toBe(1);
});

test('only published notes appear on the public page', function () {
    Note::factory()->create(['title' => 'Public']);
    Note::factory()->draft()->create(['title' => 'Draft']);

    $this->get('/note')->assertOk()->assertInertia(fn (AssertableInertia $p) => $p->has('notes', 1)->where('notes.0.title', 'Public'));
});
