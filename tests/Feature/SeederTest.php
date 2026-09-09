<?php

use App\Models\User;
use Database\Seeders\UserSeeder;

test('seeding a fresh database creates exactly one owner who can enter the admin', function () {
    $this->seed();

    expect(User::owner()->count())->toBe(1);

    $this->actingAs(User::owner()->first())->get('/dashboard')->assertOk();
});

test('seeding twice does not create a second owner', function () {
    $this->seed(UserSeeder::class);
    $this->seed(UserSeeder::class);

    expect(User::count())->toBe(1)->and(User::owner()->count())->toBe(1);
});
