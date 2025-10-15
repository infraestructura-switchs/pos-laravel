<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('remission_details', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('remission_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_cost', 14, 2);
            $table->timestamps();

            $table->foreign('remission_id')->references('id')->on('inventory_remissions');
            // products table uses the default primary key name `id`, not `product_id`
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remission_details');
    }
};


