<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomCss extends Model
{
   
    protected $table = "custom_css";

    protected $fillable = [
        'location_customizer_id',
        'live_privew',
        'card_header_background',
        'card_header_color',
        'top_header_icon_background',
        'top_header_icon_color',
        'navebar_background',
        'navebar_color',
        'navebar_grouped_background',
        'navebar_grouped_color',
        'navebar_item_active_background',
        'navebar_item_active_color',
        'navebar_item_inactive_background',
        'navebar_item_inactive_color',
        'navebar_image_color',
        'navebar_image_hover',
        'item_border_radius',
        'custom_css',
    ];

    public function locationCustomizer()
    {
        return $this->belongsTo(LocationCustomizer::class);
    }
}
