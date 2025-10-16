<?php

namespace App\Exports;

use App\Models\InventoryRemission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryRemissionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return InventoryRemission::with('warehouse', 'user')
            ->get()
            ->map(function ($r) {
                return [
                    'ID' => $r->remission_id,
                    'Folio' => $r->folio,
                    'Fecha' => $r->remission_date,
                    'Almacen' => $r->warehouse?->name,
                    'Usuario' => $r->user?->name,
                    'Concepto' => $r->concept,
                    'Nota' => $r->note,
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Folio', 'Fecha', 'Almacén', 'Usuario', 'Concepto', 'Nota'];
    }
}


