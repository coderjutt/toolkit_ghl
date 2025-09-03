<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobaViewAnnouncements extends Model
{
    protected $table="global_view_announcements";
     protected $fillable = [
        'frequency',
        'conditions',
        'user_email',
        'location_id',
        'user_id',
        'ghl_user_id',
        'announcement_id',
    ];
    protected $casts=[
        'frequency'=>'array',
        'conditions'=>'array',
    ];

     public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

}
