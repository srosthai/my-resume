<?php

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    Mail::fake();
    RateLimiter::clear('contact:127.0.0.1');
    config(['mail.contact_to' => 'owner@example.com']);
});

function contactPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'subject' => 'Hello there',
        'message' => 'I would like to work with you.',
    ], $overrides);
}

test('a valid message is mailed to the configured recipient with reply-to set', function () {
    $this->from('/contact')->post('/contact/send', contactPayload())
        ->assertRedirect('/contact')
        ->assertSessionHas('success');

    Mail::assertSent(ContactMessage::class, function (ContactMessage $mail) {
        return $mail->hasTo('owner@example.com')
            && $mail->hasReplyTo('jane@example.com')
            && $mail->data['message'] === 'I would like to work with you.';
    });
});

test('spam words and the honeypot block the message', function () {
    $this->from('/contact')->post('/contact/send', contactPayload(['message' => 'Free money, click here now!']))
        ->assertRedirect('/contact')
        ->assertSessionHasErrors('message');

    $this->from('/contact')->post('/contact/send', contactPayload(['website' => 'http://bot.example']))
        ->assertRedirect('/contact')
        ->assertSessionHasErrors('website');

    Mail::assertNothingSent();
});

test('validation errors are returned for bad input', function () {
    $this->from('/contact')->post('/contact/send', ['name' => 'J', 'email' => 'nope', 'subject' => 'hi', 'message' => 'short'])
        ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

    Mail::assertNothingSent();
});

test('mail failures never leak the exception message to the visitor', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('Resend API key invalid: re_123'));

    $response = $this->from('/contact')->post('/contact/send', contactPayload());

    $response->assertRedirect('/contact')->assertSessionHasErrors('message');
    expect(session('errors')->first('message'))
        ->not->toContain('re_123')
        ->not->toContain('Resend');
});

test('a missing recipient fails gracefully', function () {
    config(['mail.contact_to' => null]);

    $this->from('/contact')->post('/contact/send', contactPayload())->assertSessionHasErrors('message');

    Mail::assertNothingSent();
});

test('the contact endpoint is throttled per ip', function () {
    foreach (range(1, 3) as $i) {
        $this->post('/contact/send', contactPayload())->assertRedirect();
    }

    $this->post('/contact/send', contactPayload())->assertStatus(429);
});

test('feed like and view endpoints are throttled', function () {
    $feed = \App\Models\Feed::factory()->create();

    foreach (range(1, 30) as $i) {
        $this->post("/api/feeds/{$feed->id}/view")->assertOk();
    }

    $this->post("/api/feeds/{$feed->id}/like")->assertStatus(429);
});
