<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Support\CurrentClinic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CurrentClinicController extends Controller
{
    public function missing(Request $request): Response
    {
        return Inertia::render('clinic/NoClinic');
    }

    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'clinic_id' => ['required', 'integer', 'exists:clinics,id'],
        ]);

        $clinic = $request->user()?->clinics()
            ->where('clinics.id', $validated['clinic_id'])
            ->where('clinics.is_active', true)
            ->first();

        if ($clinic === null) {
            abort(403);
        }

        $request->session()->put(CurrentClinic::SESSION_KEY, $clinic->id);
        app(CurrentClinic::class)->set($clinic);

        return back();
    }
}
