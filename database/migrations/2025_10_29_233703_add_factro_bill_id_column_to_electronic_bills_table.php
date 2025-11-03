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
        Schema::table('electronic_bills', function (Blueprint $table) {
            $table->string('factro_bill_id')->unique()->nullable()->after('bill_id')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('electronic_bills', function (Blueprint $table) {
            $table->dropColumn('factro_bill_id');
        });
    }
};