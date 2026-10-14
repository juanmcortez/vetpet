<?php

namespace App\Models\Concerns;

use App\Models\Clinic;
use App\Support\CurrentClinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Apply to models with a `clinic_id` column (F2+). Use {@see scopeForCurrentClinic()} in queries
 * rather than a global scope so console jobs and admin reports stay explicit.
 */
trait BelongsToClinic
{
    /**
     * @return BelongsTo<Clinic, $this>
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForCurrentClinic(Builder $query): Builder
    {
        $clinicId = app(CurrentClinic::class)->id();

        if ($clinicId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where($this->getTable().'.clinic_id', $clinicId);
    }
}
