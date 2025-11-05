<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Str;

class CreateTenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create 
                            {id? : ID del tenant (slug)} 
                            {--name= : Nombre del tenant}
                            {--email= : Email del administrador}
                            {--domain= : Dominio personalizado (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear un nuevo tenant con su dominio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏢 Creando nuevo tenant...');
        $this->newLine();

        // Obtener ID del tenant
        $tenantId = $this->argument('id') ?? $this->ask('ID del tenant (slug, ej: empresa1)');
        
        // Validar que no exista
        if (Tenant::find($tenantId)) {
            $this->error("❌ El tenant '{$tenantId}' ya existe");
            return 1;
        }

        // Validar formato del ID
        if (!preg_match('/^[a-z0-9-]+$/', $tenantId)) {
            $this->error('❌ El ID solo puede contener letras minúsculas, números y guiones');
            return 1;
        }

        // Obtener nombre
        $name = $this->option('name') ?? $this->ask('Nombre del tenant', ucfirst($tenantId));

        // Obtener email
        $email = $this->option('email') ?? $this->ask('Email del administrador', $tenantId . '@example.com');

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Email inválido');
            return 1;
        }

        // Obtener dominio
        $defaultDomain = $tenantId . '.' . centralDomain();
        $domain = $this->option('domain') ?? $this->ask('Dominio', $defaultDomain);

        // Confirmación
        $this->newLine();
        $this->table(
            ['Campo', 'Valor'],
            [
                ['ID', $tenantId],
                ['Nombre', $name],
                ['Email', $email],
                ['Dominio', $domain],
            ]
        );

        if (!$this->confirm('¿Confirmas la creación del tenant?', true)) {
            $this->warn('Operación cancelada');
            return 0;
        }

        try {
            // Crear tenant
            $this->info('📝 Creando tenant...');
            $tenant = Tenant::create([
                'id' => $tenantId,
                'name' => $name,
                'email' => $email,
            ]);

            // Crear dominio
            $this->info('🌐 Creando dominio...');
            $tenant->domains()->create([
                'domain' => $domain,
            ]);

            $this->newLine();
            $this->info('✅ Tenant creado exitosamente!');
            $this->newLine();

            // Instrucciones adicionales
            $this->line('📋 Próximos pasos:');
            $this->newLine();

            // Verificar si el dominio ya está en hosts
            $hostsPath = 'C:\Windows\System32\drivers\etc\hosts';
            if (file_exists($hostsPath)) {
                $hostsContent = file_get_contents($hostsPath);
                
                if (strpos($hostsContent, $domain) === false && strpos($domain, '.test') !== false) {
                    $this->warn("⚠️  El dominio '{$domain}' NO está en el archivo hosts");
                    $this->line('');
                    $this->line('Ejecuta como Administrador:');
                    $this->line("  .\\add_tenant_subdomain.ps1 -Subdomain '{$tenantId}'");
                    $this->line('');
                    $this->line('O agrega manualmente a C:\\Windows\\System32\\drivers\\etc\\hosts:');
                    $this->line("  127.0.0.1       {$domain}");
                } else {
                    $this->info("✅ El dominio '{$domain}' ya está en el archivo hosts");
                }
            }

            $this->newLine();
            $this->line('Accede al tenant en:');
            $this->line("  http://{$domain}");
            $this->newLine();

            // Información de la base de datos
            $databaseName = 'tenant' . $tenantId;
            $this->line('Base de datos del tenant:');
            $this->line("  {$databaseName}");
            $this->newLine();

            $this->line('Para ejecutar migraciones en este tenant:');
            $this->line("  php artisan tenants:migrate --tenants={$tenantId}");
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error al crear el tenant: ' . $e->getMessage());
            return 1;
        }
    }
}

