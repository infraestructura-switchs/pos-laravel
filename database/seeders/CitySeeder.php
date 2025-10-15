<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener departamentos
        $bogota = Department::where('department_code', 11)->first();
        $antioquia = Department::where('department_code', 5)->first();
        $valle = Department::where('department_code', 76)->first();
        $atlantico = Department::where('department_code', 8)->first();
        $santander = Department::where('department_code', 68)->first();

        // Ciudades principales
        $cities = [
            // Bogotá
            [
                'city_code' => 11001,
                'city_name' => 'Bogotá D.C.',
                'department_id' => $bogota->id,
                'status' => 'ACTIVE',
            ],
            // Antioquia
            [
                'city_code' => 5001,
                'city_name' => 'Medellín',
                'department_id' => $antioquia->id,
                'status' => 'ACTIVE',
            ],
            [
                'city_code' => 5045,
                'city_name' => 'Apartadó',
                'department_id' => $antioquia->id,
                'status' => 'ACTIVE',
            ],
            [
                'city_code' => 5615,
                'city_name' => 'Rionegro',
                'department_id' => $antioquia->id,
                'status' => 'ACTIVE',
            ],
            // Valle del Cauca
            [
                'city_code' => 76001,
                'city_name' => 'Cali',
                'department_id' => $valle->id,
                'status' => 'ACTIVE',
            ],
            [
                'city_code' => 76111,
                'city_name' => 'Buga',
                'department_id' => $valle->id,
                'status' => 'ACTIVE',
            ],
            [
                'city_code' => 76520,
                'city_name' => 'Palmira',
                'department_id' => $valle->id,
                'status' => 'ACTIVE',
            ],
            // Atlántico
            [
                'city_code' => 8001,
                'city_name' => 'Barranquilla',
                'department_id' => $atlantico->id,
                'status' => 'ACTIVE',
            ],
            [
                'city_code' => 8758,
                'city_name' => 'Soledad',
                'department_id' => $atlantico->id,
                'status' => 'ACTIVE',
            ],
            // Santander
            [
                'city_code' => 68001,
                'city_name' => 'Bucaramanga',
                'department_id' => $santander->id,
                'status' => 'ACTIVE',
            ],
            [
                'city_code' => 68276,
                'city_name' => 'Floridablanca',
                'department_id' => $santander->id,
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['city_code' => $city['city_code']],
                $city
            );
        }
    }
}