<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    Storage::fake('uploads');
    $this->owner = User::factory()->owner()->create();
});

test('a project image can be removed without uploading a replacement', function () {
    Storage::disk('uploads')->put('projects/old.jpg', 'x');
    $project = Project::factory()->create(['image' => 'uploads/projects/old.jpg']);

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), [
            'title' => $project->title,
            'status' => 'completed',
            'remove_image' => true,
        ])
        ->assertRedirect(route('backend.projects.index'));

    expect($project->fresh()->image)->toBeNull();
    Storage::disk('uploads')->assertMissing('projects/old.jpg');
});

test('replacing a project image deletes the old file', function () {
    Storage::disk('uploads')->put('projects/old.jpg', 'x');
    $project = Project::factory()->create(['image' => 'uploads/projects/old.jpg']);

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), [
            'title' => $project->title,
            'status' => 'completed',
            'image' => UploadedFile::fake()->image('new.jpg'),
        ])
        ->assertRedirect();

    $fresh = $project->fresh();
    expect($fresh->image)->toStartWith('uploads/projects/')->not->toBe('uploads/projects/old.jpg');
    Storage::disk('uploads')->assertMissing('projects/old.jpg');
    Storage::disk('uploads')->assertExists(substr($fresh->image, strlen('uploads/')));
});

test('a project can store a gallery and the public page shows those images', function () {
    $this->actingAs($this->owner)
        ->post(route('backend.projects.store'), [
            'title' => 'Gallery Piece',
            'status' => 'completed',
            'gallery' => [
                UploadedFile::fake()->image('one.jpg'),
                UploadedFile::fake()->image('two.png'),
            ],
        ])
        ->assertRedirect(route('backend.projects.index'))
        ->assertSessionHasNoErrors();

    $project = Project::where('title', 'Gallery Piece')->first();
    expect($project->gallery)->toHaveCount(2);
    foreach ($project->gallery as $path) {
        expect($path)->toStartWith('uploads/projects/gallery/');
        Storage::disk('uploads')->assertExists(substr($path, strlen('uploads/')));
    }

    $this->get('/portfolio/gallery-piece')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->component('frontend/ProjectDetail')
        ->where('project.gallery', [
            url($project->gallery[0]),
            url($project->gallery[1]),
        ]));
});

test('updating a gallery keeps chosen images, drops the rest, and ignores foreign paths', function () {
    Storage::disk('uploads')->put('projects/gallery/keep.jpg', 'x');
    Storage::disk('uploads')->put('projects/gallery/drop.jpg', 'x');
    Storage::disk('uploads')->put('projects/other.jpg', 'x');

    $project = Project::factory()->create([
        'gallery' => ['uploads/projects/gallery/keep.jpg', 'uploads/projects/gallery/drop.jpg'],
    ]);

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), [
            'title' => $project->title,
            'status' => 'completed',
            'existing_gallery' => [
                'uploads/projects/gallery/keep.jpg',
                '../../.env',
                'uploads/projects/other.jpg',
                'https://evil.example/x.jpg',
            ],
            'gallery' => [UploadedFile::fake()->image('three.jpg')],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $fresh = $project->fresh();
    expect($fresh->gallery)->toHaveCount(2)
        ->and($fresh->gallery[0])->toBe('uploads/projects/gallery/keep.jpg')
        ->and($fresh->gallery[1])->toStartWith('uploads/projects/gallery/');

    Storage::disk('uploads')->assertExists('projects/gallery/keep.jpg');
    Storage::disk('uploads')->assertMissing('projects/gallery/drop.jpg');
    Storage::disk('uploads')->assertExists('projects/other.jpg');
    expect(file_exists(base_path('.env')))->toBeTrue();
});

test('a missing title is explained in plain language', function () {
    $project = Project::factory()->create();

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), [
            'title' => '',
            'status' => 'completed',
        ])
        ->assertSessionHasErrors(['title' => 'Title is required.']);
});

test('a project with no gallery can still be updated', function () {
    $project = Project::factory()->create(['gallery' => null, 'title' => 'Keep me']);

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), [
            'title' => 'Renamed without gallery',
            'status' => 'completed',
            'existing_gallery' => [''],
        ])
        ->assertRedirect(route('backend.projects.index'))
        ->assertSessionHasNoErrors();

    expect($project->fresh())->title->toBe('Renamed without gallery')->gallery->toBeNull();
});

test('the edit form receives a date the browser date input can show', function () {
    $project = Project::factory()->create(['created_date' => '2024-01-15']);

    $this->actingAs($this->owner)
        ->get(route('backend.projects.edit', $project))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('project.created_date', '2024-01-15'));
});

test('a title-only update leaves the gallery in place', function () {
    $project = Project::factory()->create([
        'gallery' => ['uploads/projects/gallery/keep.jpg'],
    ]);

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), [
            'title' => 'Renamed gallery',
            'status' => 'processing',
        ])
        ->assertRedirect();

    expect($project->fresh()->gallery)->toBe(['uploads/projects/gallery/keep.jpg']);
});

test('more than 12 gallery images are rejected', function () {
    $files = [];
    for ($i = 0; $i < 13; $i++) {
        $files[] = UploadedFile::fake()->image("shot-{$i}.jpg");
    }

    $this->actingAs($this->owner)
        ->post(route('backend.projects.store'), [
            'title' => 'Too many',
            'status' => 'completed',
            'gallery' => $files,
        ])
        ->assertSessionHasErrors('gallery');

    expect(Project::where('title', 'Too many')->exists())->toBeFalse();
});

test('updating without touching the image keeps it', function () {
    $project = Project::factory()->create(['image' => 'uploads/projects/keep.jpg']);

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), ['title' => 'Renamed', 'status' => 'processing'])
        ->assertRedirect();

    expect($project->fresh())->image->toBe('uploads/projects/keep.jpg')->title->toBe('Renamed');
});
