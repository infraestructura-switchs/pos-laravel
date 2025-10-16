<?php

namespace App\Exports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WarehousesExport implements FromQuery, WithMapping, WithHeadings {
    
    use Exportable;

    public function query() {
        return Warehouse::query();
    }

    public function headings(): array {
        return [
            'Nombres',
            'Celular',
            'Dirección',
        ];
    }

    public function map($Warehouse): array {

        return [
            [
                $Warehouse->name,
                $Warehouse->phone,
                $Warehouse->address,
            ],
        ];
    }
}
