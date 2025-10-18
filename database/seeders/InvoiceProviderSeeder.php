<?php

namespace Database\Seeders;

use App\Models\InvoiceProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = '2025-10-09 14:12:09';

        $invoiceProviders = [
            ['id' => 1, 'name' => 'Arquitecsoft S.A.S',    'nit' => '123123-5', 'direction' => 'calle 4', 'phone' => '2323232', 'email' => 'coreo@ars.co', 'url' => 'www.ars.co', 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 2, 'name' => 'Helltec S.A.S',       'nit' => '5555-4', 'direction' => 'Carera 1', 'phone' => '1111132', 'email' => 'corero@helltec.com', 'url' => 'www.halltec.com', 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 3, 'name' => 'Factura Electrónica Colombia',   'nit' => '', 'direction' => '', 'phone' => '', 'email' => '', 'url' => '', 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 4, 'name' => 'Siigo',                          'nit' => '', 'direction' => '', 'phone' => '', 'email' => '', 'url' => '', 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 5, 'name' => 'Alegra',                         'nit' => '', 'direction' => '', 'phone' => '', 'email' => '', 'url' => '', 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 6, 'name' => 'Contapyme',                      'nit' => '', 'direction' => '', 'phone' => '', 'email' => '', 'url' => '', 'status' => 'ACTIVE', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ];

        foreach ($invoiceProviders as $provider) {
            // Garantiza los registros con los mismos ids/timestamps que el INSERT proporcionado
            DB::table('invoice_providers')->updateOrInsert(
                ['id' => $provider['id']],
                $provider
            );
        }
    }
}