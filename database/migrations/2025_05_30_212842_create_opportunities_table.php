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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_id');
            $table->string('location_id');
            $table->string('assigned_to')->nullable();
            $table->string('contact_id');
            $table->decimal('monetary_value', 15, 2)->nullable();
            $table->string('name');
            $table->string('pipeline_id');
            $table->string('pipeline_stage_id');
            $table->string('source')->nullable();
            $table->string('status');
            $table->dateTime('date_added')->nullable();
            $table->string('company_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
