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
    Route::post('coach/{id}/status', [App\Http\Controllers\CoachController::class, 'status'])->name('coach.status');
    Route::resource('coach', App\Http\Controllers\CoachController::class);
});

require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
