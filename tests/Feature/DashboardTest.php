<?php

use App\Models\Feed;
use App\Models\Note;
use App\Models\User;
use App\Models\WorkExperience;
use Inertia\Testing\AssertableInertia;

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('the owner can visit the dashboard', function () {
    $user = User::factory()->owner()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});

test('the dashboard keeps career years as numbers and includes notes and feeds', function () {
    $owner = User::factory()->owner()->create();
    WorkExperience::factory()->create(['position' => 'Engineer', 'company' => 'Acme', 'from' => 2022, 'to' => null]);
    Note::factory()->create(['title' => 'Install Laravel']);
    Feed::factory()->create(['title' => 'Weekend market']);

    $this->actingAs($owner)->get('/dashboard')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->where('summary.workExperience.recent.0.from', 2022)
        ->where('summary.workExperience.recent.0.to', null)
        ->where('summary.notes.total', 1)
        ->where('summary.notes.published', 1)
        ->where('summary.notes.recent.0.title', 'Install Laravel')
        ->where('summary.feeds.total', 1)
        ->where('summary.feeds.published', 1)
        ->where('summary.feeds.recent.0.title', 'Weekend market'));
});

test('a non-owner user is forbidden from the dashboard and every backend route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get('/dashboard')->assertForbidden();
    $this->get('/backend/projects')->assertForbidden();
    $this->get('/backend/projects/create')->assertForbidden();
    $this->post('/backend/projects', ['title' => 'x', 'status' => 'processing'])->assertForbidden();
    $this->get('/backend/notes')->assertForbidden();
    $this->get('/backend/feeds')->assertForbidden();
    $this->get('/backend/me')->assertForbidden();
});
