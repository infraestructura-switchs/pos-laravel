<?php

namespace App\Services;

use App\Services\Factus\ApiService;
use Illuminate\Support\Facades\Log;
use App\Models\Company;

class CompanyService
{
    protected static $companyPosNitWhiteList = [
        'xxxxxx'
    ];

    public static function companyData(): array
    {
        
        $company = [
            'name' => session('config')->name,
            'nit' => session('config')->nit,
            'direction' => session('config')->direction,
            'phone' => session('config')->phone,
        ];
            
        if (FactusConfigurationService::isApiEnabled()) {
            $companyData = ApiService::companyData();

            $company = [
                'nit' => $companyData['nit'] . '-' . $companyData['dv'],
                'name' => $companyData['graphic_representation_name'],
                'direction' => (in_array($companyData['nit'], self::$companyPosNitWhiteList)? session('config')->direction : $companyData['address']),
                'phone' => $companyData['phone'],

            ];
        } 
        
        if (FactroConfigurationService::isApiEnabled()) {
            $companyData = session('config') ?? Company::first();
            Log::info('Company Data from Factro API companyData : ' ,[$companyData]);
            Log::info('Company Data from Factro API invoiceProvider : ' ,[optional($companyData)->invoiceProvider]);

            $company = [
                'nit' => $companyData->nit,
                'name' => $companyData->name,
                'direction' => $companyData->direction,
                'phone' => $companyData->phone,
                'invoice_provider' => [
                    'nit' => optional($companyData->invoiceProvider)->nit ?? $companyData->nit,
                    'name' => optional($companyData->invoiceProvider)->name ?? $companyData->name,
                    'direction' => optional($companyData->invoiceProvider)->direction ?? $companyData->direction,
                    'phone' => optional($companyData->invoiceProvider)->phone ?? $companyData->phone,
                    'url' => optional($companyData->invoiceProvider)->url ?? config('app.url'),
                ]
            ];
        }


        return $company;
    }
}
