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
        Schema::table('cash_openings', function (Blueprint $table) {
            $table->unsignedInteger('tarjeta_credito')->nullable()->default(0)->comment('monto inicial en tarjeta de crédito')->after('initial_coins');
            $table->unsignedInteger('tarjeta_debito')->nullable()->default(0)->comment('monto inicial en tarjeta de débito')->after('tarjeta_credito');
            $table->unsignedInteger('cheques')->nullable()->default(0)->comment('monto inicial en cheques')->after('tarjeta_debito');
            $table->unsignedInteger('otros')->nullable()->default(0)->comment('otros métodos de pago iniciales')->after('cheques');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_openings', function (Blueprint $table) {
            $table->dropColumn(['tarjeta_credito', 'tarjeta_debito', 'cheques', 'otros']);
        });
    }
};
