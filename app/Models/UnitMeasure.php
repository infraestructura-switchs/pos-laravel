<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitMeasure extends Model
{
    use HasFactory;

    const ACTIVE = '0';

    const INACTIVE = '1';

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => self::ACTIVE,
    ];
}
