<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_remissions', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('user_id'); // relation with users
            $table->string('folio', 20);
            $table->dateTime('remission_date');
            $table->string('concept', 50);
            $table->string('note', 200)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('user_id')->references('id')->on('users'); // Laravel default users table
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_remissions');
    }
};


