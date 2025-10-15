<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                'description' => 'Peso Colombiano',
                'acronym' => 'COP',
            ],
            [
                'description' => 'Dólar Estadounidense',
                'acronym' => 'USD',
            ],
            [
                'description' => 'Euro',
                'acronym' => 'EUR',
            ],
            [
                'description' => 'Libra Esterlina',
                'acronym' => 'GBP',
            ],
            [
                'description' => 'Yen Japonés',
                'acronym' => 'JPY',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['acronym' => $currency['acronym']],
                $currency
            );
        }
    }
}