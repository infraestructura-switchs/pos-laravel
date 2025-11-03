<?php

namespace Database\Seeders;

use App\Models\NumberingRange;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NumberingRangeSeeder extends Seeder {

    public function run() {

        NumberingRange::create([
            'prefix' => 'POS',
            'from' => 1,
            'to' => 10000000,
            'current' => 1,
            'expire' => now()->addYears(20),
            'status' => '0',
        ]);

        NumberingRange::create([
            'resolution_number' => '18760000001',
            'prefix' => 'SETT',
            'from' => 1,
            'to' => 5000000,
            'current' => 1,
            'expire' => '18-01-2030',
            'date_authorization' => '18-01-2019',
            'status' => '0',
        ]);

    }
}
