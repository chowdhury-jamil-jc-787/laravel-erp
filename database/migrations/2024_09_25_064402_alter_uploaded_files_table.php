<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            // Change file_name and file_download_url to JSON type
            $table->json('file_name')->nullable()->change(); // Make JSON type nullable
            $table->json('file_download_url')->nullable()->change(); // Change the file_download_url column to JSON
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            // Revert file_name and file_download_url to string or text (depending on the original type)
            $table->json('file_name')->nullable()->change(); // Make JSON type nullable
            $table->json('file_download_url')->nullable()->change();
        });
    }
};
