<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Clinic>
 */
class ClinicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company().' Veterinaria';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'timezone' => null,
            'currency' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withRegionalOverrides(): static
    {
        return $this->state(fn (array $attributes) => [
            'timezone' => 'America/Argentina/Cordoba',
            'currency' => 'USD',
        ]);
    }
}
