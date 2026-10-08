<?php

use App\Models\Education;
use App\Models\User;
use App\Models\WorkExperience;
use Inertia\Testing\AssertableInertia;

test('the resume lists work and education by start year, newest first', function () {
    WorkExperience::factory()->create(['title' => 'Older role', 'from' => 2016]);
    WorkExperience::factory()->create(['title' => 'Newer role', 'from' => 2022]);
    Education::factory()->create(['title' => 'Older school', 'from' => 2010]);
    Education::factory()->create(['title' => 'Newer school', 'from' => 2018]);

    $this->get('/resume')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->where('workExperience.0.title', 'Newer role')
        ->where('workExperience.1.title', 'Older role')
        ->where('education.0.title', 'Newer school')
        ->where('education.1.title', 'Older school'));
});

test('the admin career lists use the same year order', function () {
    $owner = User::factory()->owner()->create();
    WorkExperience::factory()->create(['title' => 'Older role', 'from' => 2016]);
    WorkExperience::factory()->create(['title' => 'Newer role', 'from' => 2022]);
    Education::factory()->create(['title' => 'Older school', 'from' => 2010]);
    Education::factory()->create(['title' => 'Newer school', 'from' => 2018]);

    $this->actingAs($owner)
        ->get(route('backend.work-experience.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('workExperiences.0.title', 'Newer role')
            ->where('workExperiences.1.title', 'Older role'));

    $this->actingAs($owner)
        ->get(route('backend.education.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('educations.0.title', 'Newer school')
            ->where('educations.1.title', 'Older school'));
});
