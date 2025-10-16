<?php

namespace Database\Seeders;

use App\Models\FactroConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class FactroConfigurationSeeder extends Seeder  
{
    public function run()
    {

        $configurations = [
        ];

        FactroConfiguration::create([
            'is_api_enabled' => false,
            'api' => $configurations
        ]);
    }
}
