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
        Schema::create('ghl_users2', function (Blueprint $table) {
            $table->id();

            $table->string('company_id')->nullable();          // Company reference
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();

            $table->string('type')->default('user');           // GHL user type
            $table->string('role')->default('user');           // role (admin/user etc)

            $table->string('ghl_user_id')->unique();           // GHL unique user ID
            $table->string('location_id');                     // Location reference

            $table->json('permissions')->nullable();           // JSON
            $table->json('scopes')->nullable();                // JSON
            $table->json('scopes_assigned_to_only')->nullable(); // JSON

            $table->string('user_id')->nullable();             // local app user reference (optional)
            $table->string('profile_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ghl_users2');
    }
};
