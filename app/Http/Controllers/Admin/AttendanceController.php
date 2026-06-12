<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $date     = $request->input('date', Carbon::today()->toDateString());
        $officeId = $request->input('office_id');

        $records = AttendanceRecord::with(['user', 'office'])
            ->where('date', $date)
            ->when($officeId, fn ($q) => $q->where('office_id', $officeId))
            ->orderBy('check_in_at')
            ->get();

        $offices = Office::where('is_active', true)->orderBy('name')->get();

        return view('admin.attendance.index', compact('records', 'offices', 'date', 'officeId'));
    }
}
