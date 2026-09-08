<?php

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;

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
