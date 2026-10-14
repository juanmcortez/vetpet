<?php

use App\Models\Clinic;
use App\Models\User;

test('platform admin can list clinics on admin surface', function () {
    $admin = User::factory()->create(['is_platform_admin' => true]);
    Clinic::factory()->count(2)->create();

    $this->actingAs($admin)
        ->get(vetpetUrl('admin', '/clinics'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('admin/clinics/Index'));
});

test('non admin cannot manage clinics', function () {
    $user = User::factory()->create(['is_platform_admin' => false]);

    $this->actingAs($user)
        ->get(vetpetUrl('admin', '/clinics'))
        ->assertForbidden()
        ->assertInertia(fn ($page) => $page->component('admin/NotPlatformAdmin'));
});

test('platform admin can create a clinic', function () {
    $admin = User::factory()->create(['is_platform_admin' => true]);

    $this->actingAs($admin)
        ->post(vetpetUrl('admin', '/clinics'), [
            'name' => 'Nueva Clínica',
            'slug' => 'nueva-clinica',
            'is_active' => true,
        ])
        ->assertRedirect();

    expect(Clinic::query()->where('slug', 'nueva-clinica')->exists())->toBeTrue();
});
