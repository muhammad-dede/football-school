<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return 'Home';
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('role', App\Http\Controllers\RoleController::class)->except('show');
    Route::resource('user', App\Http\Controllers\UserController::class)->except(['show', 'destroy']);
});

require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
