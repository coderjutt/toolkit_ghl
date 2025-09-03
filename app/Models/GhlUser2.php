<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GhlUser2 extends Model
{ 
    protected $table = 'ghl_users2';

    protected $fillable = [
        'ghl_user_id',
        'location_id',
        'company_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'type',
        'role',
        'permissions',
        'scopes',
        'scopes_assigned_to_only',
        'user_id',
        'profile_photo',
    ];

    protected $casts = [
        'permissions' => 'array',
        'scopes' => 'array',
        'scopes_assigned_to_only' => 'array',
    ];
}
