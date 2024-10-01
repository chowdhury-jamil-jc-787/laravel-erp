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
            // Making all columns nullable
            $table->date('date')->nullable()->change();
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->string('REP')->nullable()->change();
            $table->integer('item_quantity')->nullable()->change();
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->string('Done_by')->nullable()->change();
            $table->string('Progress')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->text('remarks')->nullable()->change();
            $table->string('Location')->nullable()->change();
            $table->string('win_status')->nullable()->change();
            $table->date('win_date')->nullable()->change();
            $table->text('download_link')->nullable(); // Include the new column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_trackers', function (Blueprint $table) {
            // Reverting changes back to their original state
            $table->date('date')->nullable(false)->change();
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->string('REP')->nullable(false)->change();
            $table->integer('item_quantity')->nullable(false)->change();
            $table->decimal('price', 10, 2)->nullable(false)->change();
            $table->string('Done_by')->nullable(false)->change();
            $table->string('Progress')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
            $table->text('remarks')->nullable(false)->change();
            $table->string('Location')->nullable(false)->change();
            $table->string('win_status')->nullable(false)->change();
            $table->date('win_date')->nullable(false)->change();
            $table->text('download_link')->nullable(false);
        });
    }
};
