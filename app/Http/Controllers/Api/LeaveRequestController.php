<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeaveRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $leaves = LeaveRequest::where('user_id', $request->user()->id)
            ->orderByDesc('start_date')
            ->get()
            ->map(fn ($l) => [
                'id'          => $l->id,
                'type'        => $l->type,
                'start_date'  => $l->start_date->toDateString(),
                'end_date'    => $l->end_date->toDateString(),
                'days'        => $l->days,
                'reason'      => $l->reason,
                'status'      => $l->status,
                'reviewed_at' => $l->reviewed_at,
            ]);

        return response()->json(['leaves' => $leaves]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'       => 'required|in:sick,annual,personal,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:500',
        ]);

        $overlap = LeaveRequest::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_date', '<=', $data['end_date'])
            ->where('end_date',   '>=', $data['start_date'])
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => 'You already have a leave request overlapping these dates.',
            ]);
        }

        $leave = LeaveRequest::create([
            ...$data,
            'user_id' => $request->user()->id,
            'status'  => 'pending',
        ]);

        return response()->json(['message' => 'Leave request submitted.', 'leave' => [
            'id'         => $leave->id,
            'type'       => $leave->type,
            'start_date' => $leave->start_date->toDateString(),
            'end_date'   => $leave->end_date->toDateString(),
            'days'       => $leave->days,
            'status'     => $leave->status,
        ]], 201);
    }

    public function destroy(Request $request, LeaveRequest $leave): JsonResponse
    {
        if ($leave->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            throw ValidationException::withMessages([
                'leave' => 'Only pending leave requests can be cancelled.',
            ]);
        }

        $leave->delete();

        return response()->json(['message' => 'Leave request cancelled.']);
    }
}
