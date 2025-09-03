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
        Schema::create('contacts_buttons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('action', ['url', 'tag'])->nullable();
            $table->string('url')->nullable();
            $table->boolean('iframe')->default(false);
            $table->string('classes')->nullable();
            $table->string('locations')->nullable();
            $table->string('folder')->nullable();
            $table->string('color')->nullable();
            $table->string('background')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts_buttons');
    }
};
