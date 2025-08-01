<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model {
    use HasFactory;

    protected $guarded = ['id'];

    public function provider(){
        return $this->belongsTo(Provider::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
