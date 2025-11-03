<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoice_providers', function (Blueprint $table) {
           $table->after('name', function ($table) {
                $table->string('nit');
                $table->string('direction')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('url')->nullable();
            });
        });
    }

    public function down()
    {
        Schema::table('invoice_providers', function (Blueprint $table) {
            $table->dropColumn('nit');
            $table->dropColumn('direction');
            $table->dropColumn('phone');
            $table->dropColumn('email');
            $table->dropColumn('url');
        });
    }
};
