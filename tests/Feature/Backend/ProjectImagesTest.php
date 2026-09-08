<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        ->assertRedirect(route('projects'));

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

test('updating without touching the image keeps it', function () {
    $project = Project::factory()->create(['image' => 'uploads/projects/keep.jpg']);

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), ['title' => 'Renamed', 'status' => 'processing'])
        ->assertRedirect();

    expect($project->fresh())->image->toBe('uploads/projects/keep.jpg')->title->toBe('Renamed');
});
