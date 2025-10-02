<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomMenuLink extends Model
{
    protected $table ="custommenulink";
    protected $fillable =[
        "Title","Url","restricted_email","action","user_id","checked"
    ];
    protected $casts=[
        //   'allowed_emails' => 'array',
          'restricted_email' => 'array',
    ];
}
