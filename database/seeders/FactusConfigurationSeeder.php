<?php

namespace Database\Seeders;

use App\Models\FactusConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class FactusConfigurationSeeder extends Seeder
{
    public function run()
    {
        $url = App::environment('production') ? 'https://api.factus.com.co/' : 'https://api-sandbox.factus.com.co/';
        $clientId = App::environment('production') ? '' : '9f561c53-d9b7-459b-9ac6-af9333b471e5';
        $clientSecret = App::environment('production') ? '' : 'WEN7SpeQc8G8qAzfYQpicvae3Ldn3rkmrzG7OjJA';
        $email = App::environment('production') ? '' : 'sandbox@factus.com.co';
        $password = App::environment('production') ? '' : 'sandbox2024%';

        $configurations = [
            'url' => $url,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'email' => $email,
            'password' => $password
        ];

        FactusConfiguration::create([
            'is_api_enabled' => true,
            'api' => $configurations
        ]);
    }
}
