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
        Schema::table('global_view_announcements', function (Blueprint $table) {
         $table->unsignedBigInteger('announcement_id')->nullable()->after('id');

            // Add foreign key constraint
            $table->foreign('announcement_id')
                  ->references('id')
                  ->on('announcements')
                  ->onDelete('cascade'); // Optional: deletes related rows if announcement is deleted
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_view_announcements', function (Blueprint $table) {
          $table->dropForeign(['announcement_id']); // Drop foreign key first
            $table->dropColumn('announcement_id');
        });
    }
};
