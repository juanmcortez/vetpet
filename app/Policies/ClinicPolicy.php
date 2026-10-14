<?php

namespace App\Policies;

use App\Models\Clinic;
use App\Models\User;
use App\Support\CurrentClinic;

class ClinicPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPlatformAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isPlatformAdmin();
    }

    public function update(User $user, Clinic $clinic): bool
    {
        if ($user->isPlatformAdmin()) {
            return true;
        }

        $current = app(CurrentClinic::class)->get();

        return $current !== null
            && $current->id === $clinic->id
            && $user->isClinicAdministrator($clinic);
    }
}
