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
        $clientId = App::environment('production') ? '' : 'a00f374d-c2d1-4136-a68a-154d9334fafd';
        $clientSecret = App::environment('production') ? '' : 'dVxSpLsvYs9YI4UEnwfEbHKZuTeSp7HIDHHBcClY';
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
            'is_api_enabled' => false,
            'api' => $configurations
        ]);
    }
}
