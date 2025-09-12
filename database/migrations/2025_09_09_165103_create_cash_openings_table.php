<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_openings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('initial_cash')->comment('dinero inicial en efectivo');
            $table->unsignedInteger('initial_coins')->default(0)->comment('monedas iniciales');
            $table->unsignedInteger('total_initial')->comment('total dinero inicial');
            $table->string('observations')->nullable()->comment('observaciones de apertura');
            $table->foreignId('user_id')->constrained()->comment('usuario que abre caja');
            $table->foreignId('terminal_id')->constrained()->comment('terminal asociada');
            $table->boolean('is_active')->default(true)->comment('indica si la caja está activa');
            $table->timestamp('opened_at')->useCurrent()->comment('fecha y hora de apertura');
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['terminal_id', 'is_active']);
            $table->index(['user_id', 'opened_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_openings');
    }
};