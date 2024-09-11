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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->date('quote_submitted_on');
            $table->string('originator');
            $table->string('location');
            $table->string('cust_name');
            $table->integer('number_of_items_quoted');
            $table->boolean('re_quote_of_past_purchases')->default(false);
            $table->boolean('opp_above_5000')->default(false);
            $table->text('funnel_opp_description')->nullable();
            $table->decimal('funnel_opp_revenue', 15, 2)->nullable();
            $table->date('expected_opp_win_by')->nullable();
            $table->enum('funnel_status', ['Win', 'Lost'])->nullable();
            $table->timestamp('last_updated')->useCurrent();
            $table->text('remarks')->nullable();
            $table->string('p_n_1')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
