<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
protected $table = "ghl_contacts";
    protected $fillable = [
        'title', 'action', 'url', 'iframe', 'classes',
        'locations', 'folder', 'color', 'background'
    ];
}
