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
        Schema::create('messages', function (Blueprint $table) {
          $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ghl_call_id', 255)->nullable();
            $table->string('message_type', 191);
            $table->string('location_id', 191);
            $table->text('attachments')->nullable();
            $table->string('contact_id', 191)->nullable();
            $table->string('conversation_id', 191)->nullable();
            $table->string('direction', 191)->nullable();
            $table->string('source', 191)->nullable();
            $table->string('subject')->nullable();
            $table->string('date_added', 225)->nullable();
            $table->string('date_updated', 255)->nullable();
            $table->longText('body')->nullable();
            $table->string('from_zip', 255)->nullable();
            $table->string('ghl_user_id', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->string('call_status', 255)->nullable();
            $table->string('call_duration', 255)->nullable();
            $table->string('content_type', 255)->nullable();
            $table->string('call_recording_url', 255)->nullable();
            $table->string('assigned_to', 255)->nullable();
            $table->string('delete_in_ghl', 255)->nullable();
            $table->string('company_id')->nullable();
            $table->string('type')->nullable();
            $table->string('message_id')->nullable();
            $table->softDeletes();
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
