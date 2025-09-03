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
        Schema::create('announcement_email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('location_id')->nullable();
            $table->string('priviet_key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_email_settings');
    }
};
