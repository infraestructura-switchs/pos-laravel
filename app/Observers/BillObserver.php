<?php

namespace App\Observers;

use App\Models\Bill;
use App\Services\FactusConfigurationService;
use App\Services\FactroConfigurationService;
use App\Services\NumberingRangeService;
use Illuminate\Support\Facades\Log;

class BillObserver
{
    public function creating(Bill $bill)
    {
        Log::info('Iniciando el proceso de creación de una nueva factura.',[
            'factus_enabled' => FactusConfigurationService::isApiEnabled(),
            'factro_enabled' => FactroConfigurationService::isApiEnabled(),
        ]);
        if (!FactusConfigurationService::isApiEnabled() 
        && !FactroConfigurationService::isApiEnabled() 
        ) {
            Log::info('La API de Facturacion Electronica no está habilitada. Obteniendo el siguiente rango de numeración.');
            $range = NumberingRangeService::nextNumber();
            $bill->number = $range->prefix.' - '.$range->current;
            $bill->numbering_range_id = $range->id;
            NumberingRangeService::incrementNumber($range);
        }
    }
}
