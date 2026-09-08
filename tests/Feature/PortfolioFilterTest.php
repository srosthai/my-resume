<?php

use App\Models\Project;
use App\Models\ProjectType;
use Inertia\Testing\AssertableInertia;

test('portfolio filters by type and search', function () {
    $web = ProjectType::factory()->create(['name' => 'Web']);
    $mobile = ProjectType::factory()->create(['name' => 'Mobile']);
    Project::factory()->create(['title' => 'Shop platform', 'project_type_id' => $web->id]);
    Project::factory()->create(['title' => 'Taxi app', 'project_type_id' => $mobile->id]);

    $this->get('/portfolio?type='.$web->id)->assertOk()->assertInertia(fn (AssertableInertia $p) => $p->has('projects', 1)->where('projects.0.title', 'Shop platform')->where('filters.type', (string) $web->id));

    $this->get('/portfolio?search=taxi')->assertOk()->assertInertia(fn (AssertableInertia $p) => $p->has('projects', 1)->where('projects.0.title', 'Taxi app'));
});

test('invalid filter input is rejected instead of causing a server error', function () {
    $this->get('/portfolio?type[]=1')->assertSessionHasErrors('type');
    $this->get('/portfolio?type=999')->assertSessionHasErrors('type');
    $this->get('/portfolio?search='.str_repeat('a', 101))->assertSessionHasErrors('search');
});

test('like wildcards in the search term are treated literally', function () {
    Project::factory()->create(['title' => '100% done']);
    Project::factory()->create(['title' => 'Nothing here']);

    $this->get('/portfolio?search=%25')->assertOk()->assertInertia(fn (AssertableInertia $p) => $p->has('projects', 1)->where('projects.0.title', '100% done'));
    $this->get('/portfolio?search=_')->assertOk()->assertInertia(fn (AssertableInertia $p) => $p->has('projects', 0));
});
