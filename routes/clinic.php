<?php

use App\Http\Controllers\Clinic\ClinicSettingsController;
use App\Http\Controllers\Clinic\CurrentClinicController;
use App\Http\Middleware\EnsureClinicAdministrator;
use App\Http\Middleware\EnsureHasClinic;
use App\Http\Middleware\SetCurrentClinic;
use Illuminate\Support\Facades\Route;

Route::middleware(SetCurrentClinic::class)->group(function (): void {
    Route::inertia('/', 'Welcome')->name('home');

    Route::middleware(['auth', 'verified'])->group(function (): void {
        Route::get('clinic/missing', [CurrentClinicController::class, 'missing'])->name('clinic.missing');
        Route::post('clinic/switch', [CurrentClinicController::class, 'switch'])->name('clinic.switch');

        Route::middleware(EnsureHasClinic::class)->group(function (): void {
            Route::inertia('dashboard', 'Dashboard')->name('dashboard');

            Route::middleware(EnsureClinicAdministrator::class)->group(function (): void {
                Route::get('settings/clinic', [ClinicSettingsController::class, 'edit'])->name('clinic.settings.edit');
                Route::patch('settings/clinic', [ClinicSettingsController::class, 'update'])->name('clinic.settings.update');
                Route::post('settings/clinic/members', [ClinicSettingsController::class, 'storeMember'])->name('clinic.team.store');
                Route::patch('settings/clinic/members/{membership}', [ClinicSettingsController::class, 'updateMember'])->name('clinic.team.update');
                Route::delete('settings/clinic/members/{membership}', [ClinicSettingsController::class, 'destroyMember'])->name('clinic.team.destroy');
            });
        });
    });

    require __DIR__.'/settings.php';
    require __DIR__.'/locale.php';

    Route::group([
        'namespace' => 'Laravel\Fortify\Http\Controllers',
        'prefix' => config('fortify.prefix', ''),
    ], function (): void {
        require base_path('vendor/laravel/fortify/routes/routes.php');
    });
});
