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
    Route::get('student/program/create/{student}', [App\Http\Controllers\StudentController::class, 'programCreate'])->name('student.program.create');
    Route::post('student/program/store/{student}', [App\Http\Controllers\StudentController::class, 'programStore'])->name('student.program.store');
    Route::get('student/program/edit/{program}', [App\Http\Controllers\StudentController::class, 'programEdit'])->name('student.program.edit');
    Route::patch('student/program/update/{program}', [App\Http\Controllers\StudentController::class, 'programUpdate'])->name('student.program.update');
    Route::delete('student/program/destroy/{program}', [App\Http\Controllers\StudentController::class, 'programDestroy'])->name('student.program.destroy');

    Route::get('student/payment/create/{billing}', [App\Http\Controllers\StudentController::class, 'paymentCreate'])->name('student.payment.create');
    Route::post('student/payment/store/{billing}', [App\Http\Controllers\StudentController::class, 'paymentStore'])->name('student.payment.store');

    Route::post('student/{student}/status', [App\Http\Controllers\StudentController::class, 'status'])->name('student.status');
    Route::post('student/{student}', [App\Http\Controllers\StudentController::class, 'update'])->name('student.update');
    Route::resource('student', App\Http\Controllers\StudentController::class)->except('update');
    // Billing
    Route::controller(App\Http\Controllers\BillingController::class)->group(function () {
        Route::get('billing', 'index')->name('billing.index');
        Route::get('billing/payment/create/{billing}', 'paymentCreate')->name('billing.payment.create');
        Route::post('billing/payment/store/{billing}', 'paymentStore')->name('billing.payment.store');
    });
    // Training
    Route::post('training/attendance/{training}', [App\Http\Controllers\TrainingController::class, 'attendance'])->name('training.attendance');
    Route::resource('training', App\Http\Controllers\TrainingController::class);
    // Match Event
    Route::post('match-event/attendance/{match_event}', [App\Http\Controllers\MatchEventController::class, 'attendance'])->name('match-event.attendance');
    Route::resource('match-event', App\Http\Controllers\MatchEventController::class);
});

require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
