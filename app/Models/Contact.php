<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $guarded =[];

    public function opportunities()
{
    return $this->hasMany(Opportunity::class, 'assigned_to','assigned_to');
}

}
