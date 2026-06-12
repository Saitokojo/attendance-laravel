<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AttendanceSummaryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\LiveStatusController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\QrSessionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('offices', OfficeController::class)->except(['destroy', 'show']);
    Route::resource('users', UserController::class)->except(['destroy', 'show']);
    Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    Route::get('qr', [QrSessionController::class, 'index'])->name('qr.index');
    Route::post('qr', [QrSessionController::class, 'store'])->name('qr.store');
    Route::get('qr/display/{office}', [QrSessionController::class, 'display'])->name('qr.display');

    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('live', [LiveStatusController::class, 'index'])->name('live.index');
    Route::get('users/{user}/summary', [AttendanceSummaryController::class, 'index'])->name('users.summary');

    Route::get('leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
    Route::patch('leaves/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
    Route::patch('leaves/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');
});

require __DIR__.'/auth.php';
