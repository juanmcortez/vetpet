<?php

namespace App\Support;

use App\Enums\ClinicRole;
use App\Models\Clinic;
use App\Models\ClinicMembership;

class ClinicMembershipGuard
{
    public static function wouldRemoveLastAdministrator(Clinic $clinic, ClinicMembership $membership): bool
    {
        if ($membership->role !== ClinicRole::Administrator) {
            return false;
        }

        return $clinic->memberships()
            ->where('role', ClinicRole::Administrator)
            ->count() <= 1;
    }

    public static function wouldDemoteLastAdministrator(
        Clinic $clinic,
        ClinicMembership $membership,
        ClinicRole $newRole,
    ): bool {
        if ($newRole === ClinicRole::Administrator) {
            return false;
        }

        return self::wouldRemoveLastAdministrator($clinic, $membership);
    }
}
