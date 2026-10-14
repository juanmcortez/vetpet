<?php

namespace App\Http\Controllers\Clinic;

use App\Enums\ClinicRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\StoreTeamMemberRequest;
use App\Http\Requests\Clinic\UpdateClinicSettingsRequest;
use App\Http\Requests\Clinic\UpdateTeamMemberRequest;
use App\Models\Clinic;
use App\Models\ClinicMembership;
use App\Models\User;
use App\Support\ClinicMembershipGuard;
use App\Support\CurrentClinic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClinicSettingsController extends Controller
{
    public function edit(Request $request): Response
    {
        $clinic = app(CurrentClinic::class)->get();

        abort_if($clinic === null, 404);

        $this->authorize('update', $clinic);

        return Inertia::render('settings/Clinic', [
            'clinic' => [
                'id' => $clinic->id,
                'name' => $clinic->name,
                'slug' => $clinic->slug,
                'timezone' => $clinic->timezone,
                'currency' => $clinic->currency,
            ],
            'members' => $this->membersPayload($clinic),
            'roles' => array_map(
                fn (ClinicRole $role) => ['value' => $role->value, 'label' => $role->value],
                ClinicRole::cases(),
            ),
        ]);
    }

    public function update(UpdateClinicSettingsRequest $request): RedirectResponse
    {
        $clinic = app(CurrentClinic::class)->get();

        abort_if($clinic === null, 404);

        $clinic->update($request->validated());

        return back()->with('status', 'clinic-settings-updated');
    }

    public function storeMember(StoreTeamMemberRequest $request): RedirectResponse
    {
        $clinic = app(CurrentClinic::class)->get();

        abort_if($clinic === null, 404);

        $validated = $request->validated();
        $user = User::query()->where('email', $validated['email'])->firstOrFail();

        if ($clinic->users()->where('users.id', $user->id)->exists()) {
            return back()->withErrors(['email' => __('vetpet.clinic.team.already_member')]);
        }

        $clinic->users()->attach($user->id, ['role' => $validated['role']]);

        return back()->with('status', 'clinic-member-added');
    }

    public function updateMember(UpdateTeamMemberRequest $request, ClinicMembership $membership): RedirectResponse
    {
        $clinic = app(CurrentClinic::class)->get();

        abort_if($clinic === null || $membership->clinic_id !== $clinic->id, 404);

        $newRole = ClinicRole::from($request->validated('role'));

        if (ClinicMembershipGuard::wouldDemoteLastAdministrator($clinic, $membership, $newRole)) {
            return back()->withErrors(['role' => __('vetpet.clinic.team.last_administrator')]);
        }

        $membership->update(['role' => $newRole]);

        return back()->with('status', 'clinic-member-updated');
    }

    public function destroyMember(Request $request, ClinicMembership $membership): RedirectResponse
    {
        $clinic = app(CurrentClinic::class)->get();

        abort_if($clinic === null || $membership->clinic_id !== $clinic->id, 404);

        $this->authorize('delete', $membership);

        if (ClinicMembershipGuard::wouldRemoveLastAdministrator($clinic, $membership)) {
            return back()->withErrors(['member' => __('vetpet.clinic.team.last_administrator')]);
        }

        $membership->delete();

        return back()->with('status', 'clinic-member-removed');
    }

    /**
     * @return list<array{id: int, role: string, user: array{id: int, name: string, email: string}}>
     */
    private function membersPayload(Clinic $clinic): array
    {
        return $clinic->memberships()
            ->with('user:id,name,email')
            ->orderBy('clinic_memberships.id')
            ->get()
            ->map(fn (ClinicMembership $membership) => [
                'id' => $membership->id,
                'role' => $membership->role->value,
                'user' => [
                    'id' => $membership->user->id,
                    'name' => $membership->user->name,
                    'email' => $membership->user->email,
                ],
            ])
            ->values()
            ->all();
    }
}
