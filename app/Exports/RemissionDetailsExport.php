<?php

namespace App\Exports;

use App\Models\RemissionDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RemissionDetailsExport implements FromQuery, WithHeadings
{
    protected $remissionId;

    public function __construct($remissionId = null)
    {
        $this->remissionId = $remissionId;
    }

    public function query()
    {
        return RemissionDetail::query()
            ->when($this->remissionId, function ($query) {
                return $query->where('remission_id', $this->remissionId);
            })
            ->with(['product', 'remission']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Producto',
            'Remisión',
            'Cantidad',
            'Costo Unitario',
            'Costo Total',
        ];
    }

    public function map($remissionDetail): array
    {
        return [
            $remissionDetail->id,
            $remissionDetail->product->name,
            $remissionDetail->remission->folio,
            $remissionDetail->quantity,
            $remissionDetail->unit_cost,
            $remissionDetail->total_cost,
        ];
    }
}