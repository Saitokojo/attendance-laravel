<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceSummaryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\StatusController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('attendance/check-in',  [AttendanceController::class, 'checkIn']);
    Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('attendance/summary',    [AttendanceSummaryController::class, 'index']);

    Route::get('status/today', [StatusController::class, 'today']);

    Route::get('leaves',              [LeaveRequestController::class, 'index']);
    Route::post('leaves',             [LeaveRequestController::class, 'store']);
    Route::delete('leaves/{leave}',   [LeaveRequestController::class, 'destroy']);
});
