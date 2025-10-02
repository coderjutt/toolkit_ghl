<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementSetting extends Model
{
    protected $fillable = ['settings', 'user_id'];
    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    protected $casts = [
        'settings' => 'array',
    ];
}
