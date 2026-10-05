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

test('the last owner cannot be deleted from the admin or from settings', function () {
    $owner = User::factory()->owner()->create();

    $this->actingAs($owner)
        ->delete(route('backend.users.destroy', $owner))
        ->assertSessionHasErrors('user')
        ->assertRedirect();

    $this->actingAs($owner)
        ->from('/settings/profile')
        ->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertSessionHasErrors('account')
        ->assertRedirect('/settings/profile');

    expect($owner->fresh())->not->toBeNull()->and(User::owner()->count())->toBe(1);

    $this->actingAs($owner)
        ->get(route('backend.users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('canDelete', false));

    $this->actingAs($owner)
        ->get('/settings/profile')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('canDeleteAccount', false));
});

test('a profile created while an owner already exists is not made owner', function () {
    $admin = User::factory()->owner()->create();
    $this->actingAs($admin);

    $this->post('/backend/users', [
        'name' => 'Profile',
        'email' => 'profile@example.com',
        'password' => 'strong-password-123',
        'password_confirmation' => 'strong-password-123',
        'dob' => '1990-01-01',
        'phone' => '012345678',
        'address' => 'Phnom Penh',
        'position' => 'Developer',
    ])->assertRedirect();

    expect(User::where('email', 'profile@example.com')->first()->is_owner)->toBeFalse();
    expect(User::owner()->count())->toBe(1);
});
