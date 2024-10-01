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
        Schema::table('quotation_trackers', function (Blueprint $table) {
            // Add the new column `uploaded_file_id`
            $table->unsignedBigInteger('uploaded_file_id')->nullable(); // Allowing null values for the foreign key initially

            // Create the foreign key constraint linking to `uploaded_files` table
            $table->foreign('uploaded_file_id')->references('id')->on('uploaded_files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_trackers', function (Blueprint $table) {
            // Drop the foreign key constraint first, then the column
            $table->dropForeign(['uploaded_file_id']);
            $table->dropColumn('uploaded_file_id');
        });
    }
};
