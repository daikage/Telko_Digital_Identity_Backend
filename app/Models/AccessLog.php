<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = [
        'access_card_id',
        'door_lock_id',
        'method',
        'status',
        'verified_at',
        'ip_address',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function accessCard()
    {
        return $this->belongsTo(AccessCard::class);
    }

    public function doorLock()
    {
        return $this->belongsTo(DoorLock::class);
    }
}
