<?php

/**
 * Script para ejecutar seeders necesarios para probar el POS
 * 
 * Uso:
 * php ejecutar_seeders_pos.php empresa1
 * 
 * O para todos los tenants:
 * php ejecutar_seeders_pos.php all
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;

// Obtener el tenant desde los argumentos
$tenantId = $argv[1] ?? null;

if (!$tenantId) {
    echo "❌ Error: Debes especificar un tenant ID\n";
    echo "Uso: php ejecutar_seeders_pos.php <tenant_id>\n";
    echo "Ejemplo: php ejecutar_seeders_pos.php empresa1\n";
    echo "O para todos: php ejecutar_seeders_pos.php all\n";
    exit(1);
}

echo "========================================\n";
echo "🌱 EJECUTANDO SEEDERS PARA POS\n";
echo "========================================\n\n";

if ($tenantId === 'all') {
    echo "📋 Ejecutando seeders para TODOS los tenants...\n\n";
} else {
    echo "📋 Ejecutando seeders para tenant: {$tenantId}\n\n";
}

// Seeders necesarios para probar el POS (en orden)
$seeders = [
    // 1. Configuración base
    'DepartmentSeeder',
    'CitySeeder',
    'CurrencySeeder',
    'InvoiceProviderSeeder',
    'TributeSeeder',
    'TaxRateSeeder',
    'PaymentMethodSeeder',
    'IdentificationDocumentSeeder',
    
    // 2. Permisos y roles
    'PermissionSeeder',
    'RoleSeeder',
    'ModuleSeeder',
    
    // 3. Empresa y usuarios
    'CompanySeeder',
    'UserSeeder',
    
    // 4. Productos y categorías
    'CategorySeeder',
    'ProductSeeder',
    
    // 5. Clientes
    'CustomerSeeder',
    
    // 6. Terminales y configuración
    'NumberingRangeSeeder',
    'TerminalSeeder',
];

$total = count($seeders);
$success = 0;
$errors = 0;

foreach ($seeders as $index => $seeder) {
    $current = $index + 1;
    echo "[{$current}/{$total}] Ejecutando {$seeder}...\n";
    
    try {
        $command = "tenants:seed --class={$seeder} --tenants={$tenantId}";
        Artisan::call($command);
        
        $output = Artisan::output();
        if (strpos($output, 'error') !== false || strpos($output, 'Error') !== false) {
            echo "  ⚠️  Advertencia en {$seeder}\n";
            $errors++;
        } else {
            echo "  ✅ {$seeder} ejecutado correctamente\n";
            $success++;
        }
    } catch (\Exception $e) {
        echo "  ❌ Error en {$seeder}: " . $e->getMessage() . "\n";
        $errors++;
    }
    
    echo "\n";
}

echo "========================================\n";
echo "📊 RESUMEN\n";
echo "========================================\n";
echo "✅ Exitosos: {$success}\n";
echo "❌ Errores: {$errors}\n";
echo "📦 Total: {$total}\n\n";

if ($errors === 0) {
    echo "🎉 ¡Todos los seeders se ejecutaron correctamente!\n";
    echo "🚀 Ya puedes probar el POS con productos, clientes y terminales.\n";
} else {
    echo "⚠️  Algunos seeders tuvieron errores. Revisa los mensajes arriba.\n";
}

