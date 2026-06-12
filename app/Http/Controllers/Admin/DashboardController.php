<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todayStats = Office::where('is_active', true)->get()->map(function (Office $office) {
            $today = Carbon::now($office->timezone)->toDateString();

            return [
                'office'      => $office,
                'checked_in'  => AttendanceRecord::where('office_id', $office->id)->where('date', $today)->count(),
                'checked_out' => AttendanceRecord::where('office_id', $office->id)->where('date', $today)->whereNotNull('check_out_at')->count(),
            ];
        });

        return view('admin.dashboard', compact('todayStats'));
    }
}
