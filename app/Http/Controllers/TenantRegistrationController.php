<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class TenantRegistrationController extends Controller
{
    /**
     * Mostrar el formulario de registro de tenant.
     */
    public function showRegistrationForm()
    {
        return view('tenant.register');
    }

    /**
     * Procesar el registro de un nuevo tenant.
     */
    public function register(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tenants,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            // Generar un subdominio único basado en el nombre de la empresa
            $subdomain = $this->generateSubdomain($validated['company_name']);

            // Crear el tenant
            $tenant = Tenant::create([
                'id' => $subdomain,
                'name' => $validated['company_name'],
                'email' => $validated['email'],
                'status' => 'active',
            ]);

            // Crear el dominio del tenant
            $tenant->domains()->create([
                'domain' => $subdomain . '.dokploy.movete.cloud',
            ]);

            // Ejecutar migraciones y seeders del tenant
            $this->setupTenantDatabase($tenant, $validated);

            // Redirigir al login del tenant con mensaje de éxito
            return redirect()
                ->away('http://' . $subdomain . '.dokploy.movete.cloud/login')
                ->with('success', '¡Empresa creada exitosamente! Ahora puedes iniciar sesión.');

        } catch (\Exception $e) {
            // Si hay un error, intentar eliminar el tenant si se creó
            if (isset($tenant)) {
                try {
                    $tenant->delete();
                } catch (\Exception $deleteException) {
                    \Log::error('Error al eliminar tenant fallido: ' . $deleteException->getMessage());
                }
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear la empresa: ' . $e->getMessage()]);
        }
    }

    /**
     * Generar un subdominio único basado en el nombre de la empresa.
     */
    private function generateSubdomain(string $companyName): string
    {
        // Convertir el nombre a un slug válido
        $slug = Str::slug($companyName);

        // Si el slug ya existe, agregar un número aleatorio
        $subdomain = $slug;
        $counter = 1;

        while (Tenant::where('id', $subdomain)->exists()) {
            $subdomain = $slug . '-' . $counter;
            $counter++;
        }

        return $subdomain;
    }

    /**
     * Configurar la base de datos del tenant.
     */
    private function setupTenantDatabase(Tenant $tenant, array $validated)
    {
        $tenant->run(function () use ($validated, $tenant) {
            // PASO 1: Ejecutar seeders básicos PRIMERO (para crear roles y permisos)
            $this->runTenantSeeders();

            // PASO 2: Crear el usuario administrador del tenant
            $userData = [
                'name' => 'Administrador',
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ];

            // Agregar phone solo si la columna existe en la tabla users
            if (\Schema::hasColumn('users', 'phone')) {
                $userData['phone'] = $validated['phone'] ?? '0000000000';
            }

            $user = User::create($userData);

            // PASO 3: Asignar el rol de Administrador al usuario
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                try {
                    $adminRole = \Spatie\Permission\Models\Role::where('name', 'Administrador')->first();

                    if ($adminRole) {
                        $user->assignRole($adminRole);
                        \Log::info("Rol Administrador asignado al usuario: " . $user->email);
                    } else {
                        \Log::warning("No se encontró el rol Administrador");
                    }
                } catch (\Exception $e) {
                    \Log::error("Error al asignar rol al usuario: " . $e->getMessage());
                }
            }

            // PASO 4: Crear la configuración de la empresa (Company)
            if (class_exists(\App\Models\Company::class)) {
                try {
                    \App\Models\Company::create([
                        'nit' => '000000000-0',
                        'name' => $tenant->name,
                        'direction' => 'Dirección por configurar',
                        'phone' => $validated['phone'] ?? '0000000000',
                        'email' => $validated['email'],
                        'type_bill' => '1',
                        'barcode' => '0',
                        'percentage_tip' => 0,
                        'department_id' => 1,
                        'city_id' => 1,
                        'currency_id' => 1,
                        'invoice_provider_id' => 1,
                    ]);
                    \Log::info("Company creada para tenant: " . $tenant->name);
                } catch (\Exception $e) {
                    \Log::warning("Error al crear Company para tenant: " . $e->getMessage());
                }
            }

            // PASO 5: Crear cliente por defecto (Consumidor Final)
            $this->createDefaultCustomer();

            \Log::info("Setup completo para tenant: " . $tenant->id);
        });
    }

    /**
     * Ejecutar seeders básicos del tenant.
     */
    private function runTenantSeeders()
    {
        try {
            // Orden de ejecución de seeders (importante para dependencias)
            $seeders = [
                // 1. Seeders de configuración base
                \Database\Seeders\DepartmentSeeder::class,
                \Database\Seeders\CitySeeder::class,
                \Database\Seeders\CurrencySeeder::class,
                \Database\Seeders\InvoiceProviderSeeder::class,
                \Database\Seeders\TributeSeeder::class,
                \Database\Seeders\TaxRateSeeder::class,
                \Database\Seeders\PaymentMethodSeeder::class,
                \Database\Seeders\IdentificationDocumentSeeder::class,

                // 2. Permisos y Roles
                \Database\Seeders\PermissionSeeder::class,
                \Database\Seeders\RoleSeeder::class,

                // 3. Módulos
                \Database\Seeders\ModuleSeeder::class,

                // 4. Configuraciones adicionales
                \Database\Seeders\TerminalSeeder::class,
            ];

            foreach ($seeders as $seederClass) {
                try {
                    if (class_exists($seederClass)) {
                        $seeder = new $seederClass();
                        $seeder->run();
                        \Log::info("Seeder ejecutado: {$seederClass}");
                    }
                } catch (\Exception $e) {
                    // Log pero continuar con los demás seeders
                    \Log::warning("Error al ejecutar seeder {$seederClass}: " . $e->getMessage());
                }
            }

            // Asignar todos los permisos al rol Administrador
            $this->assignAllPermissionsToAdmin();

        } catch (\Exception $e) {
            \Log::error("Error general al ejecutar seeders del tenant: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Asignar todos los permisos al rol Administrador.
     */
    private function assignAllPermissionsToAdmin()
    {
        try {
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'Administrador')->first();

                if ($adminRole) {
                    $allPermissions = \Spatie\Permission\Models\Permission::all();
                    $adminRole->syncPermissions($allPermissions);
                    \Log::info("Permisos asignados al rol Administrador: " . $allPermissions->count());
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Error al asignar permisos al Administrador: " . $e->getMessage());
        }
    }

    /**
     * Crear cliente por defecto (Consumidor Final).
     */
    private function createDefaultCustomer()
    {
        try {
            if (class_exists(\App\Models\Customer::class)) {
                \App\Models\Customer::create([
                    'names' => 'Consumidor Final',
                    'identification_number' => '222222222222',
                    'email' => 'cliente@general.com',
                    'phone' => '0000000000',
                    'address' => 'Dirección general',
                    'department_id' => 1,
                    'city_id' => 1,
                    'identification_document_id' => 1,
                ]);
                \Log::info("Cliente por defecto creado: Consumidor Final");
            }
        } catch (\Exception $e) {
            \Log::warning("Error al crear cliente por defecto: " . $e->getMessage());
        }
    }
}
