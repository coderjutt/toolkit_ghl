<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('location_details', function (Blueprint $table) {
            $table->id();
            $table->string('location_id')->unique();
            $table->string('company_id')->nullable();
            $table->string('name');
            $table->string('domain')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('website')->nullable();
            $table->string('timezone')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('business_logo_url')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // JSON fields
            $table->json('business')->nullable();
            $table->json('social')->nullable();
            $table->json('settings')->nullable();
            $table->json('reseller')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_details');
    }
};
