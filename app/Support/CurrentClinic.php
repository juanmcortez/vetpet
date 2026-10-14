<?php

namespace App\Support;

use App\Models\Clinic;

class CurrentClinic
{
    public const string SESSION_KEY = 'current_clinic_id';

    public function __construct(
        private ?Clinic $clinic = null,
    ) {}

    public function get(): ?Clinic
    {
        return $this->clinic;
    }

    public function id(): ?int
    {
        return $this->clinic?->id;
    }

    public function set(Clinic $clinic): void
    {
        $this->clinic = $clinic;
    }

    public function forget(): void
    {
        $this->clinic = null;
    }
}
