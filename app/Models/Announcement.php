<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'user_id',
        'ghl_location_id',
        'status',
        'expiry_type',
        'expiry_date',
        'audience_type',
        'locations',
        'title',
        'body',
        'allow_email',
        'display_setting',
        'send_email',
        'settings',
    ];

    protected $casts = [
        'locations' => 'array',
        'allow_email' => 'boolean',
        'send_email' => 'boolean',
        'settings' => 'array',
    ];
}
