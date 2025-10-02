<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
   protected $fillable = ['name', 'permissions','type'];

    protected $casts = [
        'permissions' => 'array',
    ];
}
