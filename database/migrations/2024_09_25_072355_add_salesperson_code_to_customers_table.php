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
        Schema::table('customers', function (Blueprint $table) {
                        // Add the salesperson_code column
                        $table->string('salesperson_code')->after('abn');

                        // Add foreign key constraint that references users.salesperson_code
                        $table->foreign('salesperson_code')
                              ->references('salesperson_code')
                              ->on('users')
                              ->onDelete('cascade'); // Cascade deletes if a user is deleted
                    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            Schema::table('customers', function (Blueprint $table) {
                // Drop the foreign key first before dropping the column
                $table->dropForeign(['salesperson_code']);
                $table->dropColumn('salesperson_code');
            });
        });
    }
};
