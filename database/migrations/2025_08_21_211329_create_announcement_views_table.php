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
        Schema::create('announcement_views', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('announcement_id');
            $table->unsignedBigInteger('user_id')->nullable(); // local user_id
            $table->string('ghl_user_id')->nullable();         // GHL user
            $table->string('location_id')->nullable();         // GHL location
            $table->integer('views')->default(0);              // kitni dafa user ne dekha
            $table->timestamps();

            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_views');
    }
};
