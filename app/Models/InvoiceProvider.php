<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProvider extends Model
{
    use HasFactory;

    protected $table = 'invoice_providers';
    
    protected $fillable = [
        'name',
        'status'
    ];
}