<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder {

    public function run() {
        Company::create([
            'nit' => '12345678-6',
            'name' => 'Swichts',
            'direction' => 'cra 10 # 9-04',
            'phone' => '3165584659',
            'email' => 'swichts@gmail.com',
            'type_bill' => '1',
            'barcode' => '0',
            'percentage_tip' => 0,
            'department_id' => 1,
            'city_id' => 1,
            'currency_id' => 1,
            'invoice_provider_id' => 1,
        ]);
    }
}
