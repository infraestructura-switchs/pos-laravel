<?php

namespace Database\Seeders;

use App\Models\InventoryRemission;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class InventoryRemissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there is at least one warehouse
        if (Warehouse::count() === 0) {
            Warehouse::create([ 'name' => 'Almacén Principal', 'address' => 'N/D', 'phone' => '000' ]);
        }

        // Ensure there is at least one user
        if (User::count() === 0) {
            User::factory()->create();
        }

        $warehouseIds = Warehouse::pluck('warehouse_id')->toArray();
        $userIds = User::pluck('id')->toArray();

        // Create 10 remissions with random warehouse and user
        for ($i = 0; $i < 10; $i++) {
            InventoryRemission::factory()->create([
                'warehouse_id' => Arr::random($warehouseIds),
                'user_id' => Arr::random($userIds),
            ]);
        }
    }
}


