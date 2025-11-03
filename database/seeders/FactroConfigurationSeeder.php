<?php

namespace Database\Seeders;

use App\Models\FactroConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class FactroConfigurationSeeder extends Seeder  
{
    public function run()
    {
        $urlMock = 'http://127.0.0.1:3001/arqfe-sqa-v3/arqOrchestratorController/';
        $url = App::environment('production') ? 'https://fnepacrhyrfcthdju6aeyukuoa.apigateway.us-phoenix-1.oci.customer-oci.com/arqfe-sqa-v3/arqOrchestratorController/' : 'https://fnepacrhyrfcthdju6aeyukuoa.apigateway.us-phoenix-1.oci.customer-oci.com/arqfe-sqa-v3/arqOrchestratorController/';
        $apiKeyId = App::environment('production') ? '' : '3f138400-3936-79c3-e063-1e00000c4176';
        $companyId = App::environment('production') ? '789' : '789';
        $program = App::environment('production') ? 'POS' : 'POS';
    
        $configurations = [
            'url' => $url,
            'api_key_id' => $apiKeyId,
            'company_id' => $companyId,
            'program' => $program
        ];

        FactroConfiguration::create([
            'is_api_enabled' => false,
            'api' => $configurations
        ]);
    }
}
