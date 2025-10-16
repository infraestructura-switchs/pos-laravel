<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectronicBill extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'is_validated' => 'boolean',
    ];
    
    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Accessors
    protected function numberingRange(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? json_decode($value, true) : null,
        );
    }

    // Appends
    protected function hasQrImage(): Attribute
    {
        return new Attribute(
            get: fn () => !empty($this->qr_image)
        );
    }

    protected function pdfUrl(): Attribute
    {
        return new Attribute(
            get: fn () => route('electronic-bills.pdf', ['bill' => $this->bill_id])
        );
    }

    protected function xmlUrl(): Attribute
    {
        return new Attribute(
            get: fn () => route('electronic-bills.xml', ['bill' => $this->bill_id])
        );
    }
}

