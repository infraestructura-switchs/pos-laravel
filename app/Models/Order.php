<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'products' => 'array',
        'customer' => 'array',
        'delivery_address' => 'string',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'is_available'
    ];

    /**
     * appends
     */
    public function isAvailable(): Attribute
    {
        return new Attribute(
            get: fn () => empty($this->products)
        );
    }
}