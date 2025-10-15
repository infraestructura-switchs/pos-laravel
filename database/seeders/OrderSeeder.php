<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Desactivar todas las mesas existentes
        Order::query()->update(['is_active' => false]);
    
        // Crear 20 mesas activas
        for ($i = 1; $i <= 20; $i++) {
            Order::updateOrCreate(
                ['name' => 'Mesa ' . $i],
                [
                    'products' => [],
                    'customer' => [],
                    'total' => 0,
                    'delivery_address' => '',
                    'is_active' => true
                ]
            );
        }
    }
}
