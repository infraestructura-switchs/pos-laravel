<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovementDetail extends Model
{
    use HasFactory;

    protected $table = 'stock_movements_detail';

    protected $fillable = [
        'stock_movements_id',
        'product_id',
        'quantity',
        'type', 
        'unit_cost',
        'total_cost',
        'movement_type'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:4',
    ];

    public function stockMovement()
    {
        return $this->belongsTo(StockMovement::class, 'stock_movements_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}