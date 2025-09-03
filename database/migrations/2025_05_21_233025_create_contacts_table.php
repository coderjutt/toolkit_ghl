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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->nullable();
            $table->string('location_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('source')->nullable();
            $table->string('type')->nullable();
              $table->string('company')->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('tags')->nullable();
            $table->text('custom_fields')->nullable();
            $table->dateTime('date_added')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('company_id')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
