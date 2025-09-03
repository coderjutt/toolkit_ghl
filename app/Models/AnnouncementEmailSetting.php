<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementEmailSetting extends Model
{
  use HasFactory;

    protected $fillable = [
        'from_name',
        'from_email',
        'location_id',
        'priviet_key',
    ];
}
