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
        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key
            $table->string('company_name');  // Company name
            $table->string('salesperson_code');  // Salesperson code
            $table->string('file_location');  // File location in OneDrive
            $table->string('file_download_url');  // URL to download the file
            $table->timestamps();  // Laravel's automatic created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_files');
    }
};
