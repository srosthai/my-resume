<?php

use App\Models\User;

test('public pages show the owner, not the most recently created user', function () {
    $owner = User::factory()->owner()->create(['name' => 'Real Owner', 'created_at' => now()->subYear()]);
    User::factory()->create(['name' => 'Latest Intruder']);

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('frontend/Home')->where('users.name', 'Real Owner'));

    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('user.name', 'Real Owner'));

    $this->get('/resume')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('users.name', 'Real Owner'));
});

test('a profile created while an owner already exists is not made owner', function () {
    $admin = User::factory()->owner()->create();
    $this->actingAs($admin);

    $this->post('/backend/users', [
        'name' => 'Profile',
        'email' => 'profile@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'dob' => '1990-01-01',
        'phone' => '012345678',
        'address' => 'Phnom Penh',
        'position' => 'Developer',
    ])->assertRedirect();

    expect(User::where('email', 'profile@example.com')->first()->is_owner)->toBeFalse();
    expect(User::owner()->count())->toBe(1);
});
