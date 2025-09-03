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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ghl_api_key')->nullable();
            $table->tinyInteger('role')->default(2);
            $table->tinyInteger('status')->default(1);
            $table->string('image')->nullable(); // Change to string for image path
            $table->unsignedBigInteger('added_by')->nullable(); // Corrected
            $table->string('location_id')->nullable();
            $table->integer('assigned_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['balance', 'phone', 'state', 'address', 'account_number']);
        });
    }
};
