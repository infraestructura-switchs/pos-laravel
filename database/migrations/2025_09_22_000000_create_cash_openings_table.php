<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCashOpeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cash_openings')) {
            Schema::create('cash_openings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('initial_cash')->comment('dinero inicial en efectivo');
                $table->unsignedInteger('initial_coins')->default(0)->comment('monedas iniciales');
                $table->unsignedInteger('tarjeta_credito')->nullable()->default(0)->comment('monto inicial en tarjeta de crédito');
                $table->unsignedInteger('tarjeta_debito')->default(0)->comment('monto inicial en tarjeta de débito');
                $table->unsignedInteger('cheques')->default(0)->comment('monto inicial en cheques');
                $table->unsignedInteger('otros')->default(0)->comment('otros métodos de pago iniciales');
                $table->unsignedInteger('total_initial')->comment('total dinero inicial');
                $table->string('observations', 255)->nullable()->comment('observaciones de apertura');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('terminal_id');
                $table->boolean('is_active')->default(true)->comment('indica si la caja está activa');
                $table->timestamp('opened_at')->useCurrent()->comment('fecha y hora de apertura');
                $table->timestamps();

                $table->foreign('terminal_id')->references('id')->on('terminals');
                $table->foreign('user_id')->references('id')->on('users');

                $table->index(['terminal_id', 'is_active'], 'cash_openings_terminal_id_is_active_index');
                $table->index(['user_id', 'opened_at'], 'cash_openings_user_id_opened_at_index');
            });

            // Ensure table collation matches desired collation
            DB::statement("ALTER TABLE `cash_openings` COLLATE='utf8mb4_unicode_ci'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_openings');
    }
}


