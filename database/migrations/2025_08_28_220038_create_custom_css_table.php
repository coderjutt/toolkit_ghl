<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_css', function (Blueprint $table) {
            $table->id();
            $table->boolean('live_privew')->default(false);
            $table->unsignedBigInteger('location_customizer_id');
            $table->string('card_header_background')->nullable();
            $table->string('card_header_color')->nullable();
            $table->string('top_header_icon_background')->nullable();
            $table->string('top_header_icon_color')->nullable();
            $table->string('navebar_background')->nullable();
            $table->string('navebar_color')->nullable();
            $table->string('navebar_grouped_background')->nullable();
            $table->string('navebar_grouped_color')->nullable();
            $table->string('navebar_item_active_background')->nullable();
            $table->string('navebar_item_active_color')->nullable();
            $table->string('navebar_item_inactive_background')->nullable();
            $table->string('navebar_item_inactive_color')->nullable();
            $table->string('navebar_image_color')->nullable();
            $table->string('navebar_image_hover');
            $table->string('item_border_radius')->nullable();
            $table->text('custom_css')->nullable();
            $table->foreign('location_customizer_id')
                ->references('id')
                ->on('location_customizer')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_css');
    }
};
