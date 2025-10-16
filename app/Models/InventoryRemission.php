<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryRemission extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'user_id',
        'folio',
        'remission_date',
        'concept',
        'note',
    ];

    protected $casts = [
        'remission_date' => 'date:Y-m-d', 
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function remissionDetails()
    {
        return $this->hasMany(RemissionDetail::class, 'remission_id');
    }

    public function getFormattedDateAttribute()
    {
        return $this->remission_date ? $this->remission_date->format('Y-m-d') : 'N/A';
    }
}