<?php

use App\Models\Clinic;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard when assigned to a clinic', function () {
    $clinic = Clinic::factory()->create();
    $user = createClinicMember(clinic: $clinic);

    withCurrentClinic($clinic);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});
