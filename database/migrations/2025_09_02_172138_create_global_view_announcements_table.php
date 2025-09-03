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
        Schema::create('global_view_announcements', function (Blueprint $table) {
            $table->id();
            $table->json('frequency')->nullable();   // every_page, once_per_session, etc.
            $table->json('conditions')->nullable();  // never_show_again, after_views, etc.
            $table->string('user_email')->nullable();
            $table->string('location_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('ghl_user_id')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_view_announcements');
    }
};
