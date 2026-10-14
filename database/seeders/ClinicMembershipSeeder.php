<?php

namespace Database\Seeders;

use App\Enums\ClinicRole;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClinicMembershipSeeder extends Seeder
{
    public function run(): void
    {
        $demoClinic = Clinic::query()->where('slug', 'clinica-demo')->first();

        if ($demoClinic === null) {
            return;
        }

        User::query()->each(function (User $user) use ($demoClinic): void {
            if ($user->clinics()->where('clinics.id', $demoClinic->id)->exists()) {
                return;
            }

            $user->clinics()->attach($demoClinic->id, [
                'role' => ClinicRole::Administrator->value,
            ]);
        });
    }
}
