<?php

namespace App\Http\Requests\Clinic;

use App\Enums\ClinicRole;
use App\Models\ClinicMembership;
use App\Support\CurrentClinic;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        $clinic = app(CurrentClinic::class)->get();

        return $clinic !== null
            && ($this->user()?->can('create', [ClinicMembership::class, $clinic]) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'exists:users,email'],
            'role' => ['required', Rule::enum(ClinicRole::class)],
        ];
    }
}
