<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model {

    use HasFactory;

    protected $guarded = ['id '];



    public function type() : Attribute{
        return new Attribute(
            set: fn($value) => $value ? $value : null
        );
    }

}
