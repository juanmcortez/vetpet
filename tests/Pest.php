<?php

use App\Enums\ClinicRole;
use App\Models\Clinic;
use App\Models\User;
use App\Support\CurrentClinic;
use App\Support\Vetpet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function vetpetUrl(string $surface, string $path = '/'): string
{
    return Vetpet::url($surface, $path);
}

function createClinicMember(?User $user = null, ?Clinic $clinic = null, ClinicRole $role = ClinicRole::Administrator): User
{
    $user ??= User::factory()->create();
    $clinic ??= Clinic::factory()->create();

    if (! $user->clinics()->where('clinics.id', $clinic->id)->exists()) {
        $user->clinics()->attach($clinic->id, ['role' => $role->value]);
    }

    return $user;
}

function withCurrentClinic(Clinic $clinic): void
{
    test()->session([CurrentClinic::SESSION_KEY => $clinic->id]);
}
