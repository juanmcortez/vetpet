<?php

namespace App\Policies;

use App\Models\Clinic;
use App\Models\ClinicMembership;
use App\Models\User;
use App\Support\CurrentClinic;

class ClinicMembershipPolicy
{
    public function create(User $user, Clinic $clinic): bool
    {
        return $this->canManageMembers($user, $clinic);
    }

    public function update(User $user, ClinicMembership $membership): bool
    {
        return $this->canManageMembers($user, $membership->clinic);
    }

    public function delete(User $user, ClinicMembership $membership): bool
    {
        return $this->canManageMembers($user, $membership->clinic);
    }

    protected function canManageMembers(User $user, ?Clinic $clinic): bool
    {
        if ($user->isPlatformAdmin()) {
            return true;
        }

        $clinic ??= app(CurrentClinic::class)->get();

        if ($clinic === null) {
            return false;
        }

        return $user->isClinicAdministrator($clinic);
    }
}
