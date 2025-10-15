<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCashClosingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cash_closings')) {
            Schema::create('cash_closings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('base')->comment('efectivo base');
                $table->unsignedInteger('cash')->comment('efectivo');
                $table->unsignedInteger('debit_card');
                $table->unsignedInteger('credit_card');
                $table->unsignedInteger('transfer');
                $table->unsignedInteger('total_sales');
                $table->unsignedInteger('tip')->default(0);
                $table->unsignedInteger('outputs')->comment('egresos');
                $table->unsignedInteger('gastos')->nullable()->default(0)->comment('gastos realizados durante el día');
                $table->unsignedInteger('cash_register')->comment('efectivo en caja');
                $table->unsignedInteger('price')->comment('precio real en caja');
                $table->string('observations', 255)->nullable();
                $table->unsignedBigInteger('terminal_id')->default(1);
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                $table->unsignedBigInteger('cash_opening_id')->nullable();

                $table->foreign('cash_opening_id')->references('id')->on('cash_openings');
                $table->foreign('terminal_id')->references('id')->on('terminals');
                $table->foreign('user_id')->references('id')->on('users');

                $table->index('cash_opening_id', 'cash_closings_cash_opening_id_index');
            });

            DB::statement("ALTER TABLE `cash_closings` COLLATE='utf8mb4_unicode_ci'");
        } else {
            // If table exists, ensure columns and indexes exist - add missing columns safely
            Schema::table('cash_closings', function (Blueprint $table) {
                $columns = Schema::getColumnListing('cash_closings');

                if (!in_array('cash_opening_id', $columns)) {
                    $table->unsignedBigInteger('cash_opening_id')->nullable()->after('updated_at');
                    $table->foreign('cash_opening_id')->references('id')->on('cash_openings');
                }

                if (!in_array('observations', $columns)) {
                    $table->string('observations', 255)->nullable()->after('price');
                }

                // Ensure index exists
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_map('strtolower', array_keys($sm->listTableIndexes('cash_closings')));
                if (!in_array('cash_closings_cash_opening_id_index', $indexes)) {
                    $table->index('cash_opening_id', 'cash_closings_cash_opening_id_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_closings');
    }
}


