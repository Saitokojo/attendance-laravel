<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function today(Request $request): JsonResponse
    {
        $user   = $request->user();
        $office = $user->office;
        $today  = Carbon::now($office?->timezone ?? config('app.timezone'))->toDateString();

        $record = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $leave = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if ($record) {
            $status = $record->check_out_at ? 'checked_out' : 'checked_in';
        } elseif ($leave) {
            $status = 'on_leave';
        } else {
            $status = 'not_checked_in';
        }

        return response()->json([
            'date'   => $today,
            'status' => $status,
            'record' => $record,
            'leave'  => $leave ? ['type' => $leave->type, 'end_date' => $leave->end_date] : null,
        ]);
    }
}
