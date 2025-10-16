<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('remission_id')->nullable()->constrained('inventory_remissions')->onDelete('set null');
            $table->enum('movement_type', ['IN', 'OUT']); // IN = entry, OUT = exit
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('stock_movements_date');
            $table->string('folio')->unique();
            $table->string('concept')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['movement_type', 'stock_movements_date']);
            $table->index('folio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};


