<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementView extends Model
{
    protected $fillable = [
        'announcement_id',
        'user_id',
        'email',
        'ghl_user_id',
        'location_id',
        'views',
        'audience_type',
        'frequency'
    ];
    protected $casts = [
        'frequency' => 'array',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
