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
        Schema::create('appointments', function (Blueprint $table) {
             $table->id();
            $table->string('appointment_id')->nullable();
            $table->string('location_id')->nullable();
            $table->string('calendar_id')->nullable();
            $table->text('address')->nullable();
            $table->string('title')->nullable();
            $table->string('group_id')->nullable();
            $table->string('appointment_status')->nullable();
            $table->text('users')->nullable();
            $table->text('notes')->nullable();
            $table->string('source')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->timestamp('date_added')->nullable();
            $table->timestamp('date_updated')->nullable();
            $table->string('company_id')->nullable();
            // Foreign key columns with cascade delete
            $table->string('contact_id')->nullable();
            $table->string('assigned_user_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
