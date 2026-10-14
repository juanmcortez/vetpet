<?php

use App\Models\Clinic;
use Database\Seeders\ClinicSeeder;
use Illuminate\Database\QueryException;

test('clinic can be created with factory defaults', function () {
    $clinic = Clinic::factory()->create([
        'name' => 'VETPET Norte',
        'slug' => 'vetpet-norte',
    ]);

    expect($clinic->fresh())
        ->name->toBe('VETPET Norte')
        ->slug->toBe('vetpet-norte')
        ->is_active->toBeTrue()
        ->timezone->toBeNull()
        ->currency->toBeNull()
        ->and($clinic->resolvedTimezone())->toBe(config('vetpet.timezone'))
        ->and($clinic->resolvedCurrency())->toBe(config('vetpet.currency'));
});

test('clinic slug is generated from name when omitted', function () {
    $clinic = Clinic::factory()->create([
        'name' => 'Clínica Central',
        'slug' => null,
    ]);

    expect($clinic->slug)->toBe('clinica-central');
});

test('clinic slug must be unique', function () {
    Clinic::factory()->create(['slug' => 'duplicate']);

    expect(fn () => Clinic::factory()->create(['slug' => 'duplicate']))
        ->toThrow(QueryException::class);
});

test('clinic seeder creates demo clinic', function () {
    $this->seed(ClinicSeeder::class);

    expect(Clinic::query()->where('slug', 'clinica-demo')->exists())->toBeTrue()
        ->and(Clinic::query()->count())->toBe(3);
});

test('inactive clinic factory state persists', function () {
    $clinic = Clinic::factory()->inactive()->create();

    expect($clinic->is_active)->toBeFalse();
});
