<?php

use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\ClinicMemberController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'admin/Home')->name('admin.home');

Route::middleware(['auth', 'verified', 'platform.admin'])->group(function (): void {
    Route::get('clinics', [ClinicController::class, 'index'])->name('admin.clinics.index');
    Route::post('clinics', [ClinicController::class, 'store'])->name('admin.clinics.store');
    Route::patch('clinics/{clinic}', [ClinicController::class, 'update'])->name('admin.clinics.update');
    Route::post('clinics/{clinic}/members', [ClinicMemberController::class, 'store'])->name('admin.clinics.members.store');
    Route::patch('clinics/{clinic}/members/{membership}', [ClinicMemberController::class, 'update'])->name('admin.clinics.members.update');
    Route::delete('clinics/{clinic}/members/{membership}', [ClinicMemberController::class, 'destroy'])->name('admin.clinics.members.destroy');
});

require __DIR__.'/locale.php';
