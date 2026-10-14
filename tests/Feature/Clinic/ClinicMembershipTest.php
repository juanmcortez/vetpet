<?php

use App\Enums\ClinicRole;
use App\Models\Clinic;
use App\Models\User;
use App\Support\CurrentClinic;

test('user can belong to multiple clinics', function () {
    $user = User::factory()->create();
    $first = Clinic::factory()->create();
    $second = Clinic::factory()->create();

    $user->clinics()->attach($first->id, ['role' => ClinicRole::Staff->value]);
    $user->clinics()->attach($second->id, ['role' => ClinicRole::Administrator->value]);

    expect($user->clinics)->toHaveCount(2);
});

test('authenticated user without clinic is redirected from dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('clinic.missing'));
});

test('user can switch current clinic', function () {
    $user = User::factory()->create();
    $first = Clinic::factory()->create(['name' => 'Alpha']);
    $second = Clinic::factory()->create(['name' => 'Beta']);

    $user->clinics()->attach($first->id, ['role' => ClinicRole::Staff->value]);
    $user->clinics()->attach($second->id, ['role' => ClinicRole::Staff->value]);

    $this->actingAs($user)
        ->withSession([CurrentClinic::SESSION_KEY => $first->id])
        ->post(route('clinic.switch'), ['clinic_id' => $second->id])
        ->assertRedirect();

    expect(session(CurrentClinic::SESSION_KEY))->toBe($second->id);
});

test('user cannot switch to a clinic they do not belong to', function () {
    $user = User::factory()->create();
    $foreign = Clinic::factory()->create();

    $this->actingAs($user)
        ->post(route('clinic.switch'), ['clinic_id' => $foreign->id])
        ->assertForbidden();
});
