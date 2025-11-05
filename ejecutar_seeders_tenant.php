<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\ModuleSeeder;

echo "\n=== EJECUTAR SEEDERS PARA TENANT ESPECÍFICO ===\n\n";

// Obtener el ID del tenant desde los argumentos de línea de comandos
$tenantId = $argv[1] ?? 'chuzo-de-ivan';

echo "Buscando tenant: {$tenantId}\n";

$tenant = Tenant::find($tenantId);

if (!$tenant) {
    echo "❌ ERROR: No se encontró el tenant '{$tenantId}'\n";
    echo "\nTenants disponibles:\n";
    foreach (Tenant::all() as $t) {
        echo "  - {$t->id} ({$t->name})\n";
    }
    exit(1);
}

echo "✅ Tenant encontrado: {$tenant->name} (ID: {$tenant->id})\n";
echo "Base de datos: tenant{$tenant->id}\n\n";

// Ejecutar seeders dentro del contexto del tenant
$tenant->run(function () use ($tenant) {
    echo "Ejecutando seeders dentro del contexto del tenant...\n\n";
    
    try {
        // Ejecutar PermissionSeeder
        echo "📝 Ejecutando PermissionSeeder...\n";
        $permissionSeeder = new PermissionSeeder();
        $permissionSeeder->run();
        echo "✅ PermissionSeeder ejecutado correctamente\n\n";
        
        // Ejecutar ModuleSeeder
        echo "📦 Ejecutando ModuleSeeder...\n";
        $moduleSeeder = new ModuleSeeder();
        $moduleSeeder->run();
        echo "✅ ModuleSeeder ejecutado correctamente\n\n";
        
        // Limpiar cache de permisos
        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
        echo "🔄 Cache de permisos limpiado\n\n";
        
    } catch (\Exception $e) {
        echo "❌ ERROR al ejecutar seeders: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
        exit(1);
    }
});

echo "✅ ¡Proceso completado exitosamente!\n";
echo "\nLos cambios realizados:\n";
echo "  - PermissionSeeder: Renombrado permiso 'empresas' a 'administrar empresas'\n";
echo "  - ModuleSeeder: Renombrado módulo a 'administrar empresas'\n\n";

