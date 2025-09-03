<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementSetting extends Model
{
    protected $fillable = ['settings'];
    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id');
    }
    protected $casts = [
        'settings' => 'array',
    ];
}
