<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'remission_id',
        'user_id',
        'stock_movements_date',
    ];

    protected $casts = [
        'stock_movements_date' => 'date',
    ];

    public function stockMovementDetails()
    {
        return $this->hasMany(StockMovementDetail::class, 'stock_movements_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function remission()
    {
        return $this->belongsTo(InventoryRemission::class);
    }
}