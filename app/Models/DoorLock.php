<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoorLock extends Model
{
    protected $fillable = [
        'name',
        'location',
        'lock_type',
        'ble_service_uuid',
        'ble_characteristic_uuid',
        'nfc_aid',
        'secret_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Hide the secret_key from JSON serialization by default.
     */
    protected $hidden = [
        'secret_key',
    ];

    public function accessCards()
    {
        return $this->hasMany(AccessCard::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }
}
