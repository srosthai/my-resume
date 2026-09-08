<?php

use App\Models\AboutMe;
use App\Models\Education;
use App\Models\ProjectType;
use App\Models\TechStack;
use App\Models\User;
use App\Models\WorkExperience;
use Inertia\Testing\AssertableInertia;

/**
 * The five "plain" resources share one controller shape; exercise them all.
 * [model, index route name, backend route prefix, index page, index prop, payload]
 */
$resources = [
    'about me' => [AboutMe::class, 'about-me', 'backend.about-me', 'backend/AboutMe/Index', 'aboutMes', ['title' => 'About', 'description' => 'Hi', 'location' => 'PP', 'year_experience' => '3', 'fucus_on' => 'Backend']],
    'education' => [Education::class, 'eductions', 'backend.eductions', 'backend/Education/Index', 'educations', ['title' => 'BSc', 'major' => 'IT', 'institution' => 'RUPP', 'description' => 'x', 'from' => '2018', 'to' => '2022']],
    'work experience' => [WorkExperience::class, 'work-experience', 'backend.work-experience', 'backend/WorkExperience/Index', 'workExperiences', ['title' => 'Dev', 'position' => 'Backend', 'company' => 'ACME', 'description' => 'x', 'from' => '2022', 'to' => '2024']],
    'tech stack' => [TechStack::class, 'tech-stacks', 'backend.tech-stacks', 'backend/TechStack/Index', 'techStacks', ['name' => 'Laravel', 'logo' => 'https://x/logo.svg', 'type' => 'Backend', 'description' => 'x']],
    'project type' => [ProjectType::class, 'project-types', 'backend.project-types', 'backend/ProjectType/Index', 'projectTypes', ['name' => 'Web']],
];

beforeEach(fn () => $this->owner = User::factory()->owner()->create());

test('owner can list, create, update and delete', function (string $model, string $index, string $prefix, string $page, string $prop, array $payload) {
    $this->actingAs($this->owner);

    $this->post(route("$prefix.store"), $payload)->assertRedirect(route($index))->assertSessionHas('success');
    $record = $model::first();
    expect($record)->not->toBeNull();

    $this->get(route($index))->assertOk()->assertInertia(fn (AssertableInertia $p) => $p->component($page)->has($prop, 1));
    $this->get(route("$prefix.edit", $record))->assertOk();

    $firstKey = array_key_first($payload);
    $this->put(route("$prefix.update", $record), [...$payload, $firstKey => 'Changed'])->assertRedirect(route($index));
    expect($record->fresh()->{$firstKey})->toBe('Changed');

    $this->delete(route("$prefix.destroy", $record))->assertRedirect(route($index));
    expect($model::count())->toBe(0);
})->with($resources);

test('guests are redirected and non-owners are forbidden', function (string $model, string $index, string $prefix) {
    $this->get(route($index))->assertRedirect(route('login'));
    $this->post(route("$prefix.store"), [])->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())->get(route($index))->assertForbidden();
})->with($resources);

test('validation rejects oversized input', function () {
    $this->actingAs($this->owner)
        ->post(route('backend.project-types.store'), ['name' => str_repeat('a', 256)])
        ->assertSessionHasErrors('name');

    $this->actingAs($this->owner)
        ->post(route('backend.project-types.store'), [])
        ->assertSessionHasErrors('name');
});
