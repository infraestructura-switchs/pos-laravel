<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_providers', function (Blueprint $table) {
            $table->string('nit')->nullable()->after('name');
            $table->string('direction')->nullable()->after('nit');
            $table->string('phone')->nullable()->after('direction');
            $table->string('email')->nullable()->after('phone');
            $table->string('url')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_providers', function (Blueprint $table) {
            $table->dropColumn(['nit', 'direction', 'phone', 'email', 'url']);
        });
    }
};
