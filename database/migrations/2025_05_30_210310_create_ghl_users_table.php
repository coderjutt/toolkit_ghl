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
        Schema::create('ghl_users', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->nullable();
            $table->string('role')->nullable();
            $table->string('ghl_user_id')->nullable();
            $table->json('location_id')->nullable();
            $table->json('permissions')->nullable();
            $table->json('scopes')->nullable();
            $table->json('scopes_assigned_to_only')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('profile_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ghl_users');
    }
};
