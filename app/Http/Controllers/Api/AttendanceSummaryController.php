<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceSummaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(['month' => 'nullable|date_format:Y-m']);

        $user   = $request->user();
        $month  = $request->input('month', Carbon::now()->format('Y-m'));
        $carbon = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

        $records = AttendanceRecord::where('user_id', $user->id)
            ->whereYear('date', $carbon->year)
            ->whereMonth('date', $carbon->month)
            ->orderBy('date')
            ->get()
            ->map(fn ($r) => [
                'date'         => $r->date->toDateString(),
                'check_in_at'  => $r->check_in_at,
                'check_out_at' => $r->check_out_at,
                'status'       => $r->status,
            ]);

        $approvedLeaveDays = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $carbon->copy()->endOfMonth())
            ->where('end_date',   '>=', $carbon->copy()->startOfMonth())
            ->get()
            ->sum(fn ($l) => $l->start_date->max($carbon)->diffInDays($l->end_date->min($carbon->copy()->endOfMonth())) + 1);

        return response()->json([
            'month'   => $month,
            'summary' => [
                'total_days' => $carbon->daysInMonth,
                'present'    => $records->count(),
                'on_time'    => $records->where('status', 'on_time')->count(),
                'late'       => $records->where('status', 'late')->count(),
                'on_leave'   => $approvedLeaveDays,
                'absent'     => max(0, $carbon->daysInMonth - $records->count() - $approvedLeaveDays),
            ],
            'records' => $records->values(),
        ]);
    }
}
