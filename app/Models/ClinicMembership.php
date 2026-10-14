<?php

namespace App\Models;

use App\Enums\ClinicRole;
use Database\Factories\ClinicMembershipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $clinic_id
 * @property int $user_id
 * @property ClinicRole $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Clinic $clinic
 * @property-read User $user
 */
#[Fillable(['clinic_id', 'user_id', 'role'])]
class ClinicMembership extends Pivot
{
    /** @use HasFactory<ClinicMembershipFactory> */
    use HasFactory;

    protected $table = 'clinic_memberships';

    public $incrementing = true;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => ClinicRole::class,
        ];
    }

    /**
     * @return BelongsTo<Clinic, $this>
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
