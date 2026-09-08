<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('every response carries the defence-in-depth headers and a nonce-based CSP', function () {
    $response = $this->get('/')->assertOk();

    $response->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    $csp = $response->headers->get('Content-Security-Policy');
    expect($csp)->toContain("default-src 'self'")
        ->toContain("frame-ancestors 'none'")
        ->toMatch("/script-src 'self' 'nonce-[A-Za-z0-9+\\/=]+'/");

    // The inline Ziggy script and the theme style carry the same nonce.
    preg_match("/'nonce-([^']+)'/", $csp, $m);
    $response->assertSee('nonce="'.$m[1].'"', false);
});

test('hsts is only sent over https', function () {
    $this->get('/')->assertHeaderMissing('Strict-Transport-Security');

    $this->get('https://localhost/')->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

test('guests only receive the public ziggy route group', function () {
    $this->get('/')->assertInertia(function (AssertableInertia $page) {
        $routes = $page->toArray()['props']['ziggy']['routes'];

        expect($routes)->toHaveKey('home')->toHaveKey('contact.send')->toHaveKey('api.feeds.like')
            ->not->toHaveKey('backend.projects.store')
            ->not->toHaveKey('backend.notes.destroy')
            ->not->toHaveKey('dashboard');
    });
});

test('the owner receives the full ziggy route list', function () {
    $this->actingAs(User::factory()->owner()->create())->get('/dashboard')->assertInertia(function (AssertableInertia $page) {
        expect($page->toArray()['props']['ziggy']['routes'])->toHaveKey('backend.projects.store');
    });
});

test('x-forwarded-for is honoured from a trusted proxy and ignored from an unknown one', function () {
    $cloudflare = '104.16.1.1';

    $this->get('/', ['REMOTE_ADDR' => $cloudflare, 'HTTP_X_FORWARDED_FOR' => '203.0.113.9']);
    expect(request()->ip())->toBe('203.0.113.9');

    $this->get('/', ['REMOTE_ADDR' => '198.51.100.7', 'HTTP_X_FORWARDED_FOR' => '203.0.113.9']);
    expect(request()->ip())->toBe('198.51.100.7');
});

test('short passwords are rejected by the default password rule', function () {
    config(['auth.registration_enabled' => true]);

    $this->post('/register', [
        'name' => 'Test', 'email' => 'test@example.com',
        'password' => 'short1', 'password_confirmation' => 'short1',
    ])->assertSessionHasErrors('password');
});

test('json-ld output cannot break out of its script tag', function () {
    $this->get('/')->assertOk()->assertDontSee('</script><script', false);
    // JSON_HEX_TAG turns < and > into < / > inside the ld+json payload.
    $html = $this->get('/')->getContent();
    preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
    expect($m[1])->not->toContain('<');
});
