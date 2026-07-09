<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'headline', 'bio', 'linkedin', 
        'avatar_url', 'avatar_data', 'theme_color', 'contact_email', 'contact_phone',
        'skills', 'projects', 'physical_rfid_uid'
    ];

    protected $casts = [
        'skills' => 'array',
        'projects' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class);
    }
}
