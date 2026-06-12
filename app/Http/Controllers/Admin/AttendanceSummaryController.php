<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceSummaryController extends Controller
{
    public function index(Request $request, User $user): View
    {
        $month  = $request->input('month', Carbon::now()->format('Y-m'));
        $carbon = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

        $records = AttendanceRecord::where('user_id', $user->id)
            ->whereYear('date', $carbon->year)
            ->whereMonth('date', $carbon->month)
            ->orderBy('date')
            ->get();

        $approvedLeaveDays = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $carbon->copy()->endOfMonth())
            ->where('end_date',   '>=', $carbon->copy()->startOfMonth())
            ->get()
            ->sum(fn ($l) => $l->start_date->max($carbon)->diffInDays($l->end_date->min($carbon->copy()->endOfMonth())) + 1);

        $summary = [
            'total_days'   => $carbon->daysInMonth,
            'present'      => $records->count(),
            'on_time'      => $records->where('status', 'on_time')->count(),
            'late'         => $records->where('status', 'late')->count(),
            'on_leave'     => $approvedLeaveDays,
            'absent'       => max(0, $carbon->daysInMonth - $records->count() - $approvedLeaveDays),
        ];

        $months = collect(range(0, 11))->map(fn ($i) => Carbon::now()->subMonths($i)->format('Y-m'));

        return view('admin.summary.index', compact('user', 'records', 'summary', 'month', 'months'));
    }
}
