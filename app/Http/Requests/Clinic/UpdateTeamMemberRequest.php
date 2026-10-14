<?php

namespace App\Http\Requests\Clinic;

use App\Enums\ClinicRole;
use App\Models\ClinicMembership;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ClinicMembership $membership */
        $membership = $this->route('membership');

        return $this->user()?->can('update', $membership) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', Rule::enum(ClinicRole::class)],
        ];
    }
}
