<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
  use HasFactory;
    protected $table = 'locations';
    // protected $primaryKey = 'location_id';
    protected $fillable = [
        'user_id',
        'type',
        'module_id',
        'location_id',
    ];

    // Relationships

    // Location belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Location belongs to a module
    public function module()
    {
        return $this->belongsTo(Modules::class, 'module_id');
    }
}
