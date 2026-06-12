<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');

        $leaves = LeaveRequest::with(['user.office', 'reviewer'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.leaves.index', compact('leaves', 'status'));
    }

    public function approve(LeaveRequest $leave): RedirectResponse
    {
        $leave->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "{$leave->user->name}'s leave approved.");
    }

    public function reject(LeaveRequest $leave): RedirectResponse
    {
        $leave->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "{$leave->user->name}'s leave rejected.");
    }
}
