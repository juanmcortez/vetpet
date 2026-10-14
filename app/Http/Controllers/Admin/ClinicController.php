<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClinicRequest;
use App\Http\Requests\Admin\UpdateClinicRequest;
use App\Models\Clinic;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClinicController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/clinics/Index', [
            'clinics' => Clinic::query()
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function store(StoreClinicRequest $request): RedirectResponse
    {
        Clinic::query()->create($request->validated());

        return back()->with('status', 'clinic-created');
    }

    public function update(UpdateClinicRequest $request, Clinic $clinic): RedirectResponse
    {
        $clinic->update($request->validated());

        return back()->with('status', 'clinic-updated');
    }
}
