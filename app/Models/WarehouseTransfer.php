<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WarehouseTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_warehouse_id',
        'destination_warehouse_id',
        'user_id',
        'transfer_date',
        'status',
        'folio',
        'note',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function originWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'origin_warehouse_id');
    }

    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(WarehouseTransferDetail::class);
    }

    // Método para completar el traspaso: crear los movimientos reales
    public function complete()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('El traspaso no está pendiente.');
        }

        DB::transaction(function () {
            // 1. Crear movimiento de SALIDA desde la bodega origen
            $outMovement = StockMovement::create([
                'warehouse_id' => $this->origin_warehouse_id,
                'movement_type' => 'OUT',
                'user_id' => $this->user_id,
                'stock_movements_date' => $this->transfer_date,
                'folio' => $this->folio . '-OUT',
                'concept' => 'Traspaso a ' . $this->destinationWarehouse->name,
                'note' => $this->note,
            ]);

            // 2. Crear movimiento de ENTRADA hacia la bodega destino
            $inMovement = StockMovement::create([
                'warehouse_id' => $this->destination_warehouse_id,
                'movement_type' => 'IN',
                'user_id' => $this->user_id,
                'stock_movements_date' => $this->transfer_date,
                'folio' => $this->folio . '-IN',
                'concept' => 'Traspaso desde ' . $this->originWarehouse->name,
                'note' => $this->note,
            ]);

            // 3. Crear los detalles de salida y entrada
            foreach ($this->details as $detail) {
                // Detalle de salida
                StockMovementDetail::create([
                    'stock_movements_id' => $outMovement->id,
                    'product_id' => $detail->product_id,
                    'movement_type' => 'OUT',
                    'quantity' => $detail->quantity,
                    'unit_cost' => $detail->unit_cost,
                    'total_cost' => $detail->total_cost,
                ]);

                // Detalle de entrada
                StockMovementDetail::create([
                    'stock_movements_id' => $inMovement->id,
                    'product_id' => $detail->product_id,
                    'movement_type' => 'IN',
                    'quantity' => $detail->quantity,
                    'unit_cost' => $detail->unit_cost,
                    'total_cost' => $detail->total_cost,
                ]);
            }

            // 4. Actualizar el estado del traspaso
            $this->update(['status' => 'completed']);
        });
    }

    // Método para cancelar (opcional)
    public function cancel()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('El traspaso no está pendiente.');
        }

        $this->update(['status' => 'cancelled']);
    }
}