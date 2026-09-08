<?php

use App\Models\User;

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
