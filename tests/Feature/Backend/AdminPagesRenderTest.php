<?php

use App\Models\AboutMe;
use App\Models\Education;
use App\Models\Feed;
use App\Models\Note;
use App\Models\PopularSong;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\TechStack;
use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\Route;

beforeEach(fn () => $this->owner = User::factory()->owner()->create());

test('every admin index, create and edit page renders for the owner', function () {
    $this->actingAs($this->owner);

    $records = [
        'about-me' => AboutMe::factory()->create(),
        'work-experience' => WorkExperience::factory()->create(),
        'education' => Education::factory()->create(),
        'tech-stacks' => TechStack::factory()->create(),
        'project-types' => ProjectType::factory()->create(),
        'projects' => Project::factory()->create(),
        'popular-songs' => PopularSong::factory()->create(),
        'notes' => Note::factory()->create(),
        'feeds' => Feed::factory()->create(),
    ];

    foreach ($records as $resource => $record) {
        $this->get(route("backend.$resource.index"))->assertOk();
        $this->get(route("backend.$resource.create"))->assertOk();
        $this->get(route("backend.$resource.edit", $record))->assertOk();
    }

    $this->get(route('dashboard'))->assertOk();
    $this->get(route('backend.users.index'))->assertOk();
    $this->get(route('backend.users.edit', $this->owner))->assertOk();
    $this->get(route('backend.users.show', $this->owner))->assertOk();
    $this->get(route('backend.users.delete', $this->owner))->assertOk();
    $this->get(route('backend.notes.show', $records['notes']))->assertOk();
    $this->get(route('backend.popular-songs.show', $records['popular-songs']))->assertOk();
});

test('every route name used in the Vue code exists', function () {
    $files = array_merge(
        glob(resource_path('js/**/*.vue')),
        glob(resource_path('js/**/**/*.vue')),
        glob(resource_path('js/**/**/**/*.vue')),
    );

    $missing = [];
    foreach (array_unique($files) as $file) {
        preg_match_all("/route\\(\\s*'([a-zA-Z0-9._-]+)'/", file_get_contents($file), $m);
        foreach ($m[1] as $name) {
            if (! Route::has($name)) {
                $missing[] = basename($file).': '.$name;
            }
        }
    }

    expect($missing)->toBe([]);
});
