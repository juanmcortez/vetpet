<?php

use App\Http\Controllers\Admin\ClinicController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'admin/Home')->name('admin.home');

Route::middleware(['auth', 'verified', 'platform.admin'])->group(function (): void {
    Route::get('clinics', [ClinicController::class, 'index'])->name('admin.clinics.index');
    Route::post('clinics', [ClinicController::class, 'store'])->name('admin.clinics.store');
    Route::patch('clinics/{clinic}', [ClinicController::class, 'update'])->name('admin.clinics.update');
});

require __DIR__.'/locale.php';
