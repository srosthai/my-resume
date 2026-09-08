<?php

use App\Models\PopularSong;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('uploads');
    $this->owner = User::factory()->owner()->create();
});

test('popular songs crud and the public player endpoint', function () {
    $this->actingAs($this->owner);

    $this->post(route('backend.popular-songs.store'), ['title' => 'Song', 'artist' => 'Artist', 'url' => 'https://www.youtube.com/watch?v=abc', 'duration' => 200])
        ->assertRedirect(route('popular-songs'));
    $this->post(route('backend.popular-songs.store'), ['title' => 'Bad', 'artist' => 'A', 'url' => 'not a url', 'duration' => 0])
        ->assertSessionHasErrors(['url', 'duration']);

    $song = PopularSong::first();
    expect($song->formatted_duration)->toBe('3:20');

    $this->get('/api/popular-songs')->assertOk()->assertJsonPath('0.src', 'https://www.youtube.com/watch?v=abc')->assertJsonPath('0.title', 'Song');

    $this->put(route('backend.popular-songs.update', $song), ['title' => 'Renamed', 'artist' => 'Artist', 'url' => $song->url, 'duration' => 200])->assertRedirect();
    expect($song->fresh()->title)->toBe('Renamed');

    $this->delete(route('backend.popular-songs.destroy', $song))->assertRedirect();
    expect(PopularSong::count())->toBe(0);
});

test('the owner profile can be updated with a new photo and without a password', function () {
    Storage::disk('uploads')->put('users/old.jpg', 'x');
    $this->owner->forceFill(['image' => 'uploads/users/old.jpg'])->save();

    $this->actingAs($this->owner)->put(route('backend.users.update', $this->owner), [
        'name' => 'New Name',
        'email' => $this->owner->email,
        'dob' => '1990-01-01',
        'phone' => '012345678',
        'address' => 'Phnom Penh',
        'position' => 'Developer',
        'image' => UploadedFile::fake()->image('me.png'),
    ])->assertRedirect(route('backend.users.index'))->assertSessionHasNoErrors();

    $fresh = $this->owner->fresh();
    expect($fresh->name)->toBe('New Name')->and($fresh->image)->toStartWith('uploads/users/');
    Storage::disk('uploads')->assertMissing('users/old.jpg');
});

test('a profile password change must pass the default password rule', function () {
    $this->actingAs($this->owner)->put(route('backend.users.update', $this->owner), [
        'name' => 'Owner', 'email' => $this->owner->email, 'dob' => '1990-01-01', 'phone' => '1', 'address' => 'a', 'position' => 'p',
        'password' => 'short', 'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');
});
