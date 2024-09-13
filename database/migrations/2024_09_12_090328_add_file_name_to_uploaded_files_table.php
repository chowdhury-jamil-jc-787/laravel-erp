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
        Schema::table('uploaded_files', function (Blueprint $table) {
            // Add the file_name column
            $table->string('file_name')->after('salesperson_code');  // Assuming you want to store it after salesperson_code
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            // Drop the file_name column if rolling back
            $table->dropColumn('file_name');
        });
    }
};
