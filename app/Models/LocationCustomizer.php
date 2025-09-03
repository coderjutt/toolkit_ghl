<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationCustomizer extends Model
{
    protected $table = "location_customizer";
    protected $fillable = [
        'location_id',
        'location',
        'Enable'
    ];
    public function CustomCss(array $customCss)
    {
        return $this->hasOne(CustomCss::class, 'location_customizer_id', 'id');
    }
}
