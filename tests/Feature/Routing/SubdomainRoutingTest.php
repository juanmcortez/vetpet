<?php

use App\Support\Vetpet;

test('clinic subdomain serves the staff welcome page', function () {
    $this->get(vetpetUrl('clinic'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Welcome'));
});

test('owner subdomain serves the owner home page', function () {
    $this->get(vetpetUrl('owner'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('owner/Home'));
});

test('admin subdomain serves the admin home page', function () {
    $this->get(vetpetUrl('admin'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('admin/Home'));
});

test('api subdomain exposes a health endpoint', function () {
    $this->get(vetpetUrl('api', '/health'))
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
            'service' => config('app.name'),
        ]);
});

test('api v1 user endpoint requires authentication', function () {
    $this->get(vetpetUrl('api', '/v1/user'))
        ->assertUnauthorized();
});

test('legacy up health route is not registered on the clinic domain', function () {
    $this->get(vetpetUrl('clinic', '/up'))
        ->assertNotFound();
});

test('vetpet helper resolves configured domains', function () {
    expect(Vetpet::domain('clinic'))->toBe('vet.vetpet.localhost')
        ->and(Vetpet::domain('api'))->toBe('api.vetpet.localhost');
});
