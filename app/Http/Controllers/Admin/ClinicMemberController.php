<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClinicRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClinicMemberRequest;
use App\Http\Requests\Admin\UpdateClinicMemberRequest;
use App\Models\Clinic;
use App\Models\ClinicMembership;
use App\Models\User;
use App\Support\ClinicMembershipGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClinicMemberController extends Controller
{
    public function store(StoreClinicMemberRequest $request, Clinic $clinic): RedirectResponse
    {
        $validated = $request->validated();
        $user = User::query()->where('email', $validated['email'])->firstOrFail();

        if ($clinic->users()->where('users.id', $user->id)->exists()) {
            return back()->withErrors(['email' => __('vetpet.clinic.team.already_member')]);
        }

        $clinic->users()->attach($user->id, ['role' => $validated['role']]);

        return back()->with('status', 'clinic-member-added');
    }

    public function update(UpdateClinicMemberRequest $request, Clinic $clinic, ClinicMembership $membership): RedirectResponse
    {
        abort_if($membership->clinic_id !== $clinic->id, 404);

        $newRole = ClinicRole::from($request->validated('role'));

        if (ClinicMembershipGuard::wouldDemoteLastAdministrator($clinic, $membership, $newRole)) {
            return back()->withErrors(['role' => __('vetpet.clinic.team.last_administrator')]);
        }

        $membership->update(['role' => $newRole]);

        return back()->with('status', 'clinic-member-updated');
    }

    public function destroy(Request $request, Clinic $clinic, ClinicMembership $membership): RedirectResponse
    {
        abort_if($membership->clinic_id !== $clinic->id, 404);

        $this->authorize('delete', $membership);

        if (ClinicMembershipGuard::wouldRemoveLastAdministrator($clinic, $membership)) {
            return back()->withErrors(['member' => __('vetpet.clinic.team.last_administrator')]);
        }

        $membership->delete();

        return back()->with('status', 'clinic-member-removed');
    }
}
