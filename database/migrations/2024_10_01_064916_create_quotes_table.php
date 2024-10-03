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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Name column
            $table->date('date'); // Date column
            $table->string('companyName'); // Company Name
            $table->unsignedBigInteger('quoteNo'); // Foreign key for the quote
            $table->json('tables'); // JSON column for storing array of objects

            // Foreign key constraint for the quotes table
            $table->foreign('quoteNo')->references('id')->on('quotation_trackers')->onDelete('cascade');

            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
