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
        Schema::create('pipeline_stages', function (Blueprint $table) {
           $table->id();
        $table->string('location_id')->nullable();
        $table->string('position')->nullable();
        $table->string('name')->nullable();
        $table->unsignedBigInteger('pipeline_id')->nullable(); // ✅ Fix here
        $table->string('pipeline_stage_id')->nullable();
        $table->foreign('pipeline_id')->references('id')->on('pipelines')->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};
