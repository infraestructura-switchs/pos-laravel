<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar si ya existe el usuario superadmin
        $superadmin = User::where('email', 'superadmin@gmail.com')->first();

        if (!$superadmin) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => '0000000000',
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Usuario SuperAdmin creado correctamente');
            $this->command->info('   Email: superadmin@gmail.com');
            $this->command->info('   Password: 123456');
        } else {
            $this->command->info('ℹ️  Usuario SuperAdmin ya existe');
        }
    }
}
