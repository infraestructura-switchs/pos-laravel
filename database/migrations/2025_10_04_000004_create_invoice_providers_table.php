<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('invoice_providers')) {
            Schema::create('invoice_providers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('status')->default('ACTIVE');
                $table->timestamps();
            });
        }

        // If legacy table exists, migrate data
        if (Schema::hasTable('facturadores')) {
            try {
                $rows = DB::table('facturadores')->get();
                foreach ($rows as $row) {
                    // avoid duplicates
                    $exists = DB::table('invoice_providers')->where('name', $row->nombre)->exists();
                    if (! $exists) {
                        DB::table('invoice_providers')->insert([
                            'name' => $row->nombre,
                            'status' => property_exists($row, 'status') ? $row->status : 'ACTIVE',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // ignore migration errors
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('invoice_providers');
    }
};


