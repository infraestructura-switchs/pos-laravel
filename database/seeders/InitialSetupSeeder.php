<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear registro de company si no existe
        $companyExists = DB::table('companies')->count() > 0;

        if (!$companyExists) {
            DB::table('companies')->insert([
                'name' => 'Empresa Central',
                'nit' => '000000000-0',
                'direction' => 'Dirección Principal',
                'phone' => '0000000000',
                'email' => 'info@empresa.com',
                'type_bill' => '0',
                'barcode' => '0',
                'print' => '0',
                'change' => '0',
                'tables' => '0',
                'width_ticket' => 80,
                'percentage_tip' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('✅ Configuración de empresa creada');
        } else {
            $this->command->info('ℹ️  Configuración de empresa ya existe');
        }
    }
}
