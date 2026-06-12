<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class LiveStatusController extends Controller
{
    public function index(): View
    {
        $offices = Office::where('is_active', true)->orderBy('name')->get();

        $rows = $offices->map(function (Office $office) {
            $today = Carbon::now($office->timezone)->toDateString();

            $employees = User::where('role', 'employee')
                ->where('office_id', $office->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $records = AttendanceRecord::where('office_id', $office->id)
                ->where('date', $today)
                ->get()
                ->keyBy('user_id');

            $leaves = LeaveRequest::where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->whereIn('user_id', $employees->pluck('id'))
                ->pluck('user_id')
                ->flip();

            $employees = $employees->map(function (User $emp) use ($records, $leaves) {
                $record = $records->get($emp->id);
                if ($record) {
                    $badge = $record->check_out_at ? 'checked_out' : 'checked_in';
                } elseif ($leaves->has($emp->id)) {
                    $badge = 'on_leave';
                } else {
                    $badge = 'absent';
                }
                return ['user' => $emp, 'record' => $record, 'badge' => $badge];
            });

            return ['office' => $office, 'today' => $today, 'employees' => $employees];
        });

        return view('admin.live.index', compact('rows'));
    }
}
