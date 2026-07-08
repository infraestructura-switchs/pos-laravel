<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('unit_measures', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('code', 20)->unique();
            $table->enum('status', ['0', '1'])->default('0')->comment('0 activado, 1 desactivado');
            $table->timestamps();
        });

        DB::table('unit_measures')->insert([
            ['description' => 'Unidad', 'code' => 'C62', 'status' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['description' => 'Kilogramo', 'code' => 'KGM', 'status' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['description' => 'Litro', 'code' => 'LTR', 'status' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['description' => 'Metro', 'code' => 'MTR', 'status' => '0', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('unit_measures');
    }
};
