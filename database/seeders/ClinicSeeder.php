<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    /**
     * Seed demo clinics for local development and manual QA.
     */
    public function run(): void
    {
        Clinic::query()->firstOrCreate(
            ['slug' => 'clinica-demo'],
            [
                'name' => 'Clínica Demo',
                'is_active' => true,
            ],
        );

        Clinic::factory()->count(2)->create();
    }
}
