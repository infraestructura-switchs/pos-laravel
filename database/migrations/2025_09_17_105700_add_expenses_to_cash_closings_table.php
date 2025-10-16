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
        Schema::table('cash_closings', function (Blueprint $table) {
            $table->unsignedInteger('gastos')->nullable()->default(0)->comment('gastos realizados durante el día')->after('outputs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_closings', function (Blueprint $table) {
            $table->dropColumn('gastos');
        });
    }
};
