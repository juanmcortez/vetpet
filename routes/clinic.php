<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/locale.php';

Route::group([
    'namespace' => 'Laravel\Fortify\Http\Controllers',
    'prefix' => config('fortify.prefix', ''),
], function (): void {
    require base_path('vendor/laravel/fortify/routes/routes.php');
});
