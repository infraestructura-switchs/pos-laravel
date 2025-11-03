<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    use HasFactory;

    protected $guarded = ['id'];

    // Campos asignables en masa
    protected $fillable = [
        'nit',
        'name',
        'direction',
        'phone',
        'email',
        'type_bill',
        'barcode',
        'print',
        'change',
        'tables',
        'width_ticket',
        'percentage_tip',
        'department_id',
        'city_id',
        'currency_id',
        'invoice_provider_id',
    ];



    #accessor and mutator
    protected function percentageTip(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int) $value
        );
    }

    #appends
    protected function formatPercentageTip(): Attribute
    {
        return new Attribute(
            get: fn () => $this->percentage_tip / 100
        );
    }

    public function invoiceProvider()
    {
        return $this->belongsTo(InvoiceProvider::class, 'invoice_provider_id');
    }
    
}
