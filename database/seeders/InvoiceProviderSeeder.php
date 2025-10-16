<?php

namespace Database\Seeders;

use App\Models\InvoiceProvider;
use Illuminate\Database\Seeder;

class InvoiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invoiceProviders = [
            [
                'name' => 'Facturador Electrónico DIAN',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Facturador Gratuito DIAN',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Factura Electrónica Colombia',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Siigo',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Alegra',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Contapyme',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($invoiceProviders as $provider) {
            InvoiceProvider::updateOrCreate(
                ['name' => $provider['name']],
                $provider
            );
        }
    }
}