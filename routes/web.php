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
    // Role
    Route::resource('role', App\Http\Controllers\RoleController::class)->except('show');
    // User
    Route::post('user/{user}/status', [App\Http\Controllers\UserController::class, 'status'])->name('user.status');
    Route::resource('user', App\Http\Controllers\UserController::class)->except('show');
    // Period
    Route::post('group/{group}/status', [App\Http\Controllers\GroupController::class, 'status'])->name('group.status');
    Route::resource('group', App\Http\Controllers\GroupController::class)->except(['show', 'destroy']);
    // Period
    Route::post('period/{period}/status', [App\Http\Controllers\PeriodController::class, 'status'])->name('period.status');
    Route::resource('period', App\Http\Controllers\PeriodController::class)->except(['show', 'destroy']);
    // Coach
    Route::post('coach/{coach}/status', [App\Http\Controllers\CoachController::class, 'status'])->name('coach.status');
    Route::post('coach/{coach}', [App\Http\Controllers\CoachController::class, 'update'])->name('coach.update');
    Route::resource('coach', App\Http\Controllers\CoachController::class)->except('update');
    // Student
    Route::get('student/enrollment/create/{student}', [App\Http\Controllers\StudentController::class, 'enrollmentCreate'])->name('student.enrollment.create');
    Route::post('student/enrollment/store/{student}', [App\Http\Controllers\StudentController::class, 'enrollmentStore'])->name('student.enrollment.store');
    Route::get('student/enrollment/edit/{enrollment}', [App\Http\Controllers\StudentController::class, 'enrollmentEdit'])->name('student.enrollment.edit');
    Route::patch('student/enrollment/update/{enrollment}', [App\Http\Controllers\StudentController::class, 'enrollmentUpdate'])->name('student.enrollment.update');
    Route::delete('student/enrollment/destroy/{enrollment}', [App\Http\Controllers\StudentController::class, 'enrollmentDestroy'])->name('student.enrollment.destroy');

    Route::get('student/payment/create/{billing}', [App\Http\Controllers\StudentController::class, 'paymentCreate'])->name('student.payment.create');
    Route::post('student/payment/store/{billing}', [App\Http\Controllers\StudentController::class, 'paymentStore'])->name('student.payment.store');

    Route::post('student/{student}/status', [App\Http\Controllers\StudentController::class, 'status'])->name('student.status');
    Route::post('student/{student}', [App\Http\Controllers\StudentController::class, 'update'])->name('student.update');
    Route::resource('student', App\Http\Controllers\StudentController::class)->except('update');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
