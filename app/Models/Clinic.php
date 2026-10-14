<?php

namespace App\Models;

use Database\Factories\ClinicFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $timezone
 * @property string|null $currency
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'timezone', 'currency', 'is_active'])]
class Clinic extends Model
{
    /** @use HasFactory<ClinicFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Clinic $clinic): void {
            if (blank($clinic->slug) && filled($clinic->name)) {
                $clinic->slug = Str::slug($clinic->name);
            }
        });
    }

    public function resolvedTimezone(): string
    {
        return $this->timezone ?? (string) config('vetpet.timezone');
    }

    public function resolvedCurrency(): string
    {
        return $this->currency ?? (string) config('vetpet.currency');
    }
}
