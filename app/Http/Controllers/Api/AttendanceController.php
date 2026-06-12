<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckInRequest;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        $record = $this->attendanceService->checkIn(
            $request->user(),
            $request->validated('token'),
            (float) $request->validated('latitude'),
            (float) $request->validated('longitude'),
        );

        return response()->json(['message' => 'Check-in recorded.', 'record' => $record], 201);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $record = $this->attendanceService->checkOut($request->user());

        return response()->json(['message' => 'Check-out recorded.', 'record' => $record]);
    }
}
