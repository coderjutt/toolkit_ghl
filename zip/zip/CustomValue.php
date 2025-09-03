<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomValue extends Model
{
    protected $fillable = [
        'company_email',
        'agent_name',
    ];
}
