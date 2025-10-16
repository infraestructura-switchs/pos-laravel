<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('stock_movements_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_movements_id')->constrained('stock_movements')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->enum('movement_type', ['IN', 'OUT'])->default('IN');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->index('stock_movements_id');
            $table->index('product_id');
        });
    }


    public function down()
    {
        Schema::dropIfExists('stock_movements_detail');
        Schema::dropIfExists('stock_movements');
    }
};