<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationCustomizer extends Model
{
    protected $table = "location_customizer";

    protected $fillable = [
        'location_id',
        'location',
        'Enable',
        'user_id'
    ];

    // ✅ Relation with User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ✅ Relation with CustomCss
    public function customCss()
    {
        return $this->hasOne(CustomCss::class, 'location_customizer_id', 'id');
    }
}
