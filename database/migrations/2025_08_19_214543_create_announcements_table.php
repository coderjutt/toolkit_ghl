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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();

            // GHL Location (sub-account)
            $table->string('ghl_location_id')->nullable();

            $table->string('status')->default('active'); // active, push
            $table->string('expiry_type')->default('never'); // never, date
            $table->dateTime('expiry_date')->nullable();

            $table->string('audience_type')->default('all'); // all, specific
            $table->json('locations')->nullable();

            $table->string('title');
            $table->text('body');

            $table->boolean('allow_email')->default(false);
            $table->string('display_setting')->default('never_again'); // never_again, stop_after_1_view
            $table->boolean('send_email')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
