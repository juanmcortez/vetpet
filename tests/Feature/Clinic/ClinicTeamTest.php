<?php

use App\Enums\ClinicRole;
use App\Models\Clinic;
use App\Models\ClinicMembership;
use App\Models\User;

test('clinic administrator can open clinic settings', function () {
    $clinic = Clinic::factory()->create();
    $admin = createClinicMember(role: ClinicRole::Administrator, clinic: $clinic);

    withCurrentClinic($clinic);

    $this->actingAs($admin)
        ->get(route('clinic.settings.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('settings/Clinic'));
});

test('clinic staff cannot open clinic settings', function () {
    $clinic = Clinic::factory()->create();
    $staff = createClinicMember(role: ClinicRole::Staff, clinic: $clinic);

    withCurrentClinic($clinic);

    $this->actingAs($staff)
        ->get(route('clinic.settings.edit'))
        ->assertForbidden();
});

test('clinic administrator can add a member by email', function () {
    $clinic = Clinic::factory()->create();
    $admin = createClinicMember(role: ClinicRole::Administrator, clinic: $clinic);
    $invite = User::factory()->create(['email' => 'new-member@example.com']);

    withCurrentClinic($clinic);

    $this->actingAs($admin)
        ->post(route('clinic.team.store'), [
            'email' => $invite->email,
            'role' => ClinicRole::Staff->value,
        ])
        ->assertRedirect();

    expect($clinic->users()->where('users.id', $invite->id)->exists())->toBeTrue();
});

test('cannot remove the last clinic administrator', function () {
    $clinic = Clinic::factory()->create();
    $admin = createClinicMember(role: ClinicRole::Administrator, clinic: $clinic);
    $membership = ClinicMembership::query()
        ->where('clinic_id', $clinic->id)
        ->where('user_id', $admin->id)
        ->firstOrFail();

    withCurrentClinic($clinic);

    $this->actingAs($admin)
        ->delete(route('clinic.team.destroy', $membership))
        ->assertRedirect()
        ->assertSessionHasErrors('member');
});

test('platform admin can assign a user to a clinic from admin surface', function () {
    $platformAdmin = User::factory()->create(['is_platform_admin' => true]);
    $clinic = Clinic::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($platformAdmin)
        ->post(vetpetUrl('admin', "/clinics/{$clinic->id}/members"), [
            'email' => $user->email,
            'role' => ClinicRole::Staff->value,
        ])
        ->assertRedirect();

    expect($clinic->users()->where('users.id', $user->id)->exists())->toBeTrue();
});

test('clinic administrator can update clinic profile', function () {
    $clinic = Clinic::factory()->create(['name' => 'Before']);
    $admin = createClinicMember(role: ClinicRole::Administrator, clinic: $clinic);

    withCurrentClinic($clinic);

    $this->actingAs($admin)
        ->patch(route('clinic.settings.update'), [
            'name' => 'After',
            'timezone' => 'America/Argentina/Buenos_Aires',
            'currency' => 'ARS',
        ])
        ->assertRedirect();

    expect($clinic->fresh()->name)->toBe('After');
});
