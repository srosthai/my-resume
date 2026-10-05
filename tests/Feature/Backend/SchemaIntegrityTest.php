<?php

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\DB;

beforeEach(fn () => $this->owner = User::factory()->owner()->create());

test('deleting a project type nulls the type on its projects instead of leaving orphans', function () {
    $type = ProjectType::factory()->create();
    $project = Project::factory()->create(['project_type_id' => $type->id]);

    $this->actingAs($this->owner)->delete(route('backend.project-types.destroy', $type))->assertRedirect();

    expect($project->fresh()->project_type_id)->toBeNull();
});

test('a profile description longer than 255 characters is accepted', function () {
    $long = str_repeat('Backend developer. ', 40); // ~760 chars

    $this->actingAs($this->owner)->put(route('backend.users.update', $this->owner), [
        'name' => 'Owner',
        'email' => $this->owner->email,
        'dob' => '1990-01-01',
        'phone' => '012345678',
        'address' => str_repeat('Street 271, Phnom Penh. ', 15),
        'position' => 'Developer',
        'description' => $long,
    ])->assertRedirect(route('backend.users.index'))->assertSessionHasNoErrors();

    expect($this->owner->fresh()->description)->toBe(trim($long));
});

test('project status is cast to an enum and validated against it', function () {
    $project = Project::factory()->create(['status' => 'completed']);
    expect($project->fresh()->status)->toBe(ProjectStatus::Completed);

    $this->actingAs($this->owner)
        ->put(route('backend.projects.update', $project), ['title' => 'x', 'status' => 'bogus'])
        ->assertSessionHasErrors('status');
});

test('status columns are strings, so a new value does not need a database migration', function () {
    DB::table('projects')->insert([
        'title' => 'Paused work',
        'slug' => 'paused-work',
        'status' => 'paused',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(DB::table('projects')->where('status', 'paused')->exists())->toBeTrue();
});

test('career periods are stored as years and Present means still current', function () {
    $this->actingAs($this->owner)
        ->post(route('backend.work-experience.store'), [
            'title' => 'Now',
            'position' => 'Engineer',
            'company' => 'Acme',
            'from' => '2022',
            'to' => 'Present',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $job = WorkExperience::query()->first();
    expect($job->from)->toBe(2022)->and($job->to)->toBeNull();

    $this->post(route('backend.work-experience.store'), [
        'title' => 'Bad',
        'from' => 'soon',
        'to' => '2010',
    ])->assertSessionHasErrors('from');
});

test('deleting a project, note, or feed hides it until it is restored', function () {
    $project = Project::factory()->create(['title' => 'Restorable']);
    $slug = $project->slug;

    $this->actingAs($this->owner)->delete(route('backend.projects.destroy', $project))->assertRedirect();

    expect(Project::find($project->id))->toBeNull()
        ->and(Project::withTrashed()->find($project->id))->not->toBeNull()
        ->and(Project::where('slug', $slug)->exists())->toBeFalse();

    $again = Project::factory()->create(['title' => 'Restorable']);
    expect($again->slug)->not->toBe($slug);

    $this->post(route('backend.projects.restore', $project->id))->assertRedirect();
    expect(Project::find($project->id))->not->toBeNull();
});
