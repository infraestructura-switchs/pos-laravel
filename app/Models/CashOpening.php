<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashOpening extends Model
{
    use HasFactory;

    protected $fillable = [
        'initial_cash',
        'initial_coins',
        'total_initial',
        'observations',
        'user_id',
        'terminal_id',
        'is_active',
        'opened_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'is_active' => 'boolean',
        'initial_cash' => 'integer',
        'initial_coins' => 'integer',
        'total_initial' => 'integer',
    ];

    /**
     * Relación con el usuario que abrió la caja
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la terminal
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    /**
     * Relación con el cierre de caja asociado
     */
    public function cashClosing(): BelongsTo
    {
        return $this->belongsTo(CashClosing::class, 'id', 'cash_opening_id');
    }

    /**
     * Scope para obtener solo las cajas activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para obtener por terminal
     */
    public function scopeByTerminal($query, $terminalId)
    {
        return $query->where('terminal_id', $terminalId);
    }

    /**
     * Verificar si hay una caja abierta para la terminal
     */
    public static function hasOpenCash($terminalId): bool
    {
        return self::where('terminal_id', $terminalId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Obtener la apertura activa de una terminal
     */
    public static function getActiveCash($terminalId): ?self
    {
        return self::where('terminal_id', $terminalId)
            ->where('is_active', true)
            ->latest('opened_at')
            ->first();
    }

    /**
     * Cerrar esta apertura de caja
     */
    public function close(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Accessor para formatear el total inicial
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_initial, 0);
    }
}