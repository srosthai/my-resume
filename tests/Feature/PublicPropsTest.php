<?php

use App\Models\Feed;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    User::factory()->owner()->create([
        'name' => 'Owner',
        'email' => 'owner@example.com',
        'dob' => '1990-01-01',
        'phone' => '012345678',
        'address' => 'Secret street',
    ]);
});

$private = ['dob', 'email_verified_at', 'password', 'remember_token', 'created_at', 'updated_at', 'is_owner'];

test('home and about pages expose only public owner fields', function () use ($private) {
    foreach (['/' => 'users', '/about' => 'user'] as $url => $key) {
        $this->get($url)->assertOk()->assertInertia(function (AssertableInertia $page) use ($key, $private) {
            $page->where("$key.name", 'Owner');
            foreach ([...$private, 'email', 'phone', 'address'] as $field) {
                $page->missing("$key.$field");
            }
        });
    }
});

test('the resume page shows contact details but no date of birth or account fields', function () use ($private) {
    $this->get('/resume')->assertOk()->assertInertia(function (AssertableInertia $page) use ($private) {
        $page->where('users.email', 'owner@example.com')->where('users.phone', '012345678');
        foreach ($private as $field) {
            $page->missing("users.$field");
        }
    });
});

test('public feeds expose only id, name and image of the author', function () {
    Feed::factory()->create(['user_id' => User::owner()->first()->id]);

    $this->get('/feeds')->assertOk()->assertInertia(function (AssertableInertia $page) {
        $page->has('feeds', 1)
            ->where('feeds.0.user.name', 'Owner')
            ->missing('feeds.0.user.email')
            ->missing('feeds.0.user.phone')
            ->missing('feeds.0.user.dob')
            ->missing('feeds.0.user.address');
    });
});

test('the shared auth user is trimmed to the admin shell fields', function () {
    $this->actingAs(User::owner()->first())->get('/dashboard')->assertOk()->assertInertia(function (AssertableInertia $page) {
        $page->where('auth.user.name', 'Owner')
            ->where('auth.user.is_owner', true)
            ->missing('auth.user.dob')
            ->missing('auth.user.phone')
            ->missing('auth.user.address');
    });
});
