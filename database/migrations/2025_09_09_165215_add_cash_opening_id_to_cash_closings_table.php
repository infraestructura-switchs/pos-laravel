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
        Schema::table('cash_closings', function (Blueprint $table) {
            $table->foreignId('cash_opening_id')->nullable()->constrained()->comment('relación con apertura de caja');
            $table->index('cash_opening_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_closings', function (Blueprint $table) {
            $table->dropForeign(['cash_opening_id']);
            $table->dropColumn('cash_opening_id');
        });
    }
};