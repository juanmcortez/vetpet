<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'admin/Home')->name('admin.home');

require __DIR__.'/locale.php';
