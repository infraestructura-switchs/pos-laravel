<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            [
                'department_code' => 5,
                'department_name' => 'Antioquia',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 8,
                'department_name' => 'Atlántico',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 11,
                'department_name' => 'Bogotá D.C.',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 13,
                'department_name' => 'Bolívar',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 15,
                'department_name' => 'Boyacá',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 17,
                'department_name' => 'Caldas',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 19,
                'department_name' => 'Cauca',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 20,
                'department_name' => 'Cesar',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 23,
                'department_name' => 'Córdoba',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 25,
                'department_name' => 'Cundinamarca',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 27,
                'department_name' => 'Chocó',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 41,
                'department_name' => 'Huila',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 44,
                'department_name' => 'La Guajira',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 47,
                'department_name' => 'Magdalena',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 50,
                'department_name' => 'Meta',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 52,
                'department_name' => 'Nariño',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 54,
                'department_name' => 'Norte de Santander',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 63,
                'department_name' => 'Quindío',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 66,
                'department_name' => 'Risaralda',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 68,
                'department_name' => 'Santander',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 70,
                'department_name' => 'Sucre',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 73,
                'department_name' => 'Tolima',
                'status' => 'ACTIVE',
            ],
            [
                'department_code' => 76,
                'department_name' => 'Valle del Cauca',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['department_code' => $department['department_code']],
                $department
            );
        }
    }
}