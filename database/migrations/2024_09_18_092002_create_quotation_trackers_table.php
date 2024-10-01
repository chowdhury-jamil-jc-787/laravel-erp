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
        Schema::create('quotation_trackers', function (Blueprint $table) {
            $table->id();
            $table->date('date');                                     // Date of quotation
            $table->unsignedBigInteger('customer_id');                // Customer foreign key
            $table->string('REP');                                    // Foreign key to users table (salesperson_code)
            $table->integer('item_quantity');                         // Number of items
            $table->decimal('price', 10, 2);                          // Price of the quotation
            $table->string('Done_by');                    // User who handled the quote
            $table->string('Progress');                               // Status/Progress of the quotation
            $table->date('start_date');                               // Start date of the quotation process
            $table->date('end_date')->nullable();                     // End date (nullable if not finished)
            $table->text('remarks')->nullable();                      // Remarks/notes
            $table->string('Location');                               // Location of the quotation/project
            $table->string('win_status')->default(false);            // Whether the quotation was won
            $table->date('win_date')->nullable();                     // Date the quotation was won
            $table->timestamps();                                     // Created at and updated at timestamps

            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('REP')->references('salesperson_code')->on('users')->onDelete('cascade');
            $table->foreign('Done_by')->references('salesperson_code')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_trackers');
    }
};
