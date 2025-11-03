<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehouse_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origin_warehouse_id');
            $table->unsignedBigInteger('destination_warehouse_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('transfer_date');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->string('folio')->unique();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('origin_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_transfers');
    }
};