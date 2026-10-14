<?php

use App\Support\Locale;
use App\Support\Money;
use Illuminate\Support\Facades\App;

test('locale middleware applies cookie preference', function () {
    $response = $this->withUnencryptedCookie('locale', 'en')
        ->get(vetpetUrl('clinic'));

    $response->assertOk();
    expect(App::getLocale())->toBe('en');
});

test('locale can be updated and remembered', function () {
    $response = $this->post(vetpetUrl('clinic', '/locale'), [
        'locale' => 'en',
    ]);

    $response->assertRedirect();
    $response->assertPlainCookie('locale', 'en');
    expect(App::getLocale())->toBe('en');
});

test('unsupported locale update is rejected', function () {
    $this->from(vetpetUrl('clinic'))
        ->post(vetpetUrl('clinic', '/locale'), [
            'locale' => 'fr',
        ])
        ->assertSessionHasErrors('locale');
});

test('locale resolves from accept language header', function () {
    expect(Locale::resolveFromAcceptLanguage('es-AR,es;q=0.9,en;q=0.8'))->toBe('es')
        ->and(Locale::resolveFromAcceptLanguage('en-US,en;q=0.9'))->toBe('en');
});

test('money formats argentine pesos', function () {
    config([
        'vetpet.currency' => 'ARS',
        'vetpet.currency_locale' => 'es_AR',
    ]);

    $formatted = Money::format(1234.5);

    expect($formatted)->toContain('1.234,50')
        ->and($formatted)->toMatch('/\$|ARS/u');
});
