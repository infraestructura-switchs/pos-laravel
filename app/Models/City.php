<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'city';
    
    protected $fillable = [
        'city_code',
        'city_name',
        'department_id',
        'status'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}