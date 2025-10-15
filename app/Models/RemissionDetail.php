<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemissionDetail extends Model
{
    protected $table = 'remission_details';
    protected $fillable = ['remission_id', 'product_id', 'quantity', 'unit_cost', 'total_cost'];

    public function remission()
    {
        return $this->belongsTo(InventoryRemission::class, 'remission_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}