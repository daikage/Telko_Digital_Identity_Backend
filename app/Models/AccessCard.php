<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccessCard extends Model
{
    protected $fillable = [
        'user_id',
        'door_lock_id',
        'card_token',
        'card_name',
        'is_active',
        'expires_at',
        'access_schedule',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'access_schedule' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doorLock()
    {
        return $this->belongsTo(DoorLock::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    /**
     * Check if this access card is currently valid.
     * Validates: active status, expiry, and time-of-day schedule.
     */
    public function isValid(): bool
    {
        // Must be active
        if (!$this->is_active) {
            return false;
        }

        // Must not be expired
        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) {
            return false;
        }

        // Check access schedule if defined
        if ($this->access_schedule) {
            return $this->isWithinSchedule();
        }

        return true;
    }

    /**
     * Check if the current time falls within the access schedule.
     */
    private function isWithinSchedule(): bool
    {
        $schedule = $this->access_schedule;
        $now = Carbon::now();

        // Check day of week
        if (isset($schedule['days']) && is_array($schedule['days'])) {
            $currentDay = strtolower($now->format('D')); // mon, tue, wed...
            if (!in_array($currentDay, array_map('strtolower', $schedule['days']))) {
                return false;
            }
        }

        // Check time window
        if (isset($schedule['start']) && isset($schedule['end'])) {
            $currentTime = $now->format('H:i');
            if ($currentTime < $schedule['start'] || $currentTime > $schedule['end']) {
                return false;
            }
        }

        return true;
    }
}
