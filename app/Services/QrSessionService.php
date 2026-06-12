<?php

namespace App\Services;

use App\Models\Office;
use App\Models\QrSession;
use App\Models\User;
use Illuminate\Support\Str;

class QrSessionService
{
    public function startSession(Office $office, User $admin): QrSession
    {
        return QrSession::create([
            'token'      => (string) Str::uuid(),
            'office_id'  => $office->id,
            'created_by' => $admin->id,
            'starts_at'  => now(),
            'expires_at' => now()->addMinutes(30),
        ]);
    }

    public function currentActive(Office $office): ?QrSession
    {
        return QrSession::where('office_id', $office->id)
            ->where('expires_at', '>', now())
            ->latest('starts_at')
            ->first();
    }
}
