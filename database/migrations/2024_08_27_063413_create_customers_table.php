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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('address_2')->nullable();
            $table->string('city');
            $table->string('territory_code');
            $table->string('currency_code');
            $table->string('post_code');
            $table->string('county')->nullable();
            $table->string('abn');
            $table->string('country_region_code');
            $table->string('location_code');
            $table->string('phone_no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
