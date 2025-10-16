<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentation extends Model {

    use HasFactory;

    protected $attributes = [
        'status' => '0'
    ];

    protected $guarded=['id'];

    protected $casts = [
        'price' => 'integer',
        'quantity' => 'integer',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    
}
