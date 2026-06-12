<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'user_id',
        'office_id',
        'date',
        'check_in_session_id',
        'check_in_at',
        'check_in_lat',
        'check_in_lng',
        'check_out_session_id',
        'check_out_at',
        'check_out_lat',
        'check_out_lng',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_at' => 'datetime',
            'check_in_lat' => 'decimal:7',
            'check_in_lng' => 'decimal:7',
            'check_out_at' => 'datetime',
            'check_out_lat' => 'decimal:7',
            'check_out_lng' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function checkInSession(): BelongsTo
    {
        return $this->belongsTo(QrSession::class, 'check_in_session_id');
    }

    public function checkOutSession(): BelongsTo
    {
        return $this->belongsTo(QrSession::class, 'check_out_session_id');
    }
}
