<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model
{
    protected $fillable = ['profile_id', 'viewer_ip'];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
