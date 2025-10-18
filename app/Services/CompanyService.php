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
            Log::info('Company Data from Factro API invoiceProvider : ' ,[$companyData->invoiceProvider]);

            $company = [
                'invoice_provider' => [
                    'nit' => $companyData->invoiceProvider->nit,
                    'name' => $companyData->invoiceProvider->name,
                    'direction' => $companyData->invoiceProvider->direction,
                    'phone' => $companyData->invoiceProvider->phone,
                    'url' => $companyData->invoiceProvider->url,
                ]
            ];
        }    


        return $company;
    }
}
