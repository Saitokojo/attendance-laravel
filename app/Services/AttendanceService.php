<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\QrSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function checkIn(User $user, string $token, float $lat, float $lng): AttendanceRecord
    {
        $session = QrSession::where('token', $token)->first();

        if (! $session || $session->expires_at->isPast()) {
            throw ValidationException::withMessages(['token' => 'QR code is expired or invalid.']);
        }

        $office = $user->office;

        if (! $office || $user->office_id !== $session->office_id) {
            throw ValidationException::withMessages(['token' => 'QR code does not belong to your office.']);
        }

        $date = Carbon::now($office->timezone)->toDateString();

        if (AttendanceRecord::where('user_id', $user->id)->where('date', $date)->exists()) {
            throw ValidationException::withMessages(['token' => 'You have already checked in today.']);
        }

        if (! $this->withinGeofence($lat, $lng, (float) $office->latitude, (float) $office->longitude, (float) $office->radius_meters)) {
            throw ValidationException::withMessages(['location' => 'You are outside the office geofence.']);
        }

        $nowInOffice = Carbon::now($office->timezone);
        [$h, $m, $s] = array_pad(explode(':', $office->late_after), 3, '00');
        $cutoff      = $nowInOffice->copy()->setTime((int) $h, (int) $m, (int) $s);
        $status      = $nowInOffice->gt($cutoff) ? 'late' : 'on_time';

        return AttendanceRecord::create([
            'user_id'             => $user->id,
            'office_id'           => $office->id,
            'date'                => $date,
            'check_in_session_id' => $session->id,
            'check_in_at'         => now(),
            'check_in_lat'        => $lat,
            'check_in_lng'        => $lng,
            'status'              => $status,
        ]);
    }

    public function checkOut(User $user): AttendanceRecord
    {
        $office = $user->office;
        $date   = Carbon::now($office?->timezone ?? config('app.timezone'))->toDateString();

        $record = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $date)
            ->whereNull('check_out_at')
            ->first();

        if (! $record) {
            throw ValidationException::withMessages(['checkout' => 'No open check-in found for today.']);
        }

        $record->update(['check_out_at' => now()]);

        return $record->fresh();
    }

    private function withinGeofence(float $userLat, float $userLng, float $officeLat, float $officeLng, float $radiusMeters): bool
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($officeLat - $userLat);
        $dLng = deg2rad($officeLng - $userLng);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($userLat)) * cos(deg2rad($officeLat)) * sin($dLng / 2) ** 2;

        $distance = $earthRadius * 2 * asin(sqrt($a));

        return $distance <= $radiusMeters;
    }
}
