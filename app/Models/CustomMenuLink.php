<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomMenuLink extends Model
{
    protected $table ="custommenulink";
    protected $fillable =[
        "Title","allowed_emails","restricted_email","action"
    ];
    protected $casts=[
          'allowed_emails' => 'array',
          'restricted_email' => 'array',
    ];
}
