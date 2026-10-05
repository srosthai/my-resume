<?php

use App\Models\User;

test('registration is disabled by default', function () {
    config(['auth.registration_enabled' => false]);

    $this->get('/register')->assertNotFound();

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'correct-horse-battery-9',
        'password_confirmation' => 'correct-horse-battery-9',
    ])->assertNotFound();

    $this->assertGuest();
    expect(User::count())->toBe(0);
});

test('login page hides the sign-up link when registration is disabled', function () {
    config(['auth.registration_enabled' => false]);

    $this->get('/login')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('auth/Login')->where('canRegister', false));
});

test('registration screen can be rendered when enabled', function () {
    config(['auth.registration_enabled' => true]);

    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register when enabled', function () {
    config(['auth.registration_enabled' => true]);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'correct-horse-battery-9',
        'password_confirmation' => 'correct-horse-battery-9',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('profile.edit'));
});

test('a registered user is never the owner', function () {
    config(['auth.registration_enabled' => true]);

    $this->post('/register', [
        'name' => 'Intruder',
        'email' => 'intruder@example.com',
        'password' => 'correct-horse-battery-9',
        'password_confirmation' => 'correct-horse-battery-9',
    ]);

    expect(User::where('email', 'intruder@example.com')->first()->is_owner)->toBeFalse();
});
