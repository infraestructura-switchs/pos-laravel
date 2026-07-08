<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->after('category_id', function (Blueprint $table) {
                $table->foreignId('unit_measure_id')->nullable()->constrained('unit_measures')->nullOnDelete();
            });
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['unit_measure_id']);
            $table->dropColumn('unit_measure_id');
        });
    }
};
