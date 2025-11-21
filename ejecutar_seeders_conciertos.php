<?php

/**
 * Script para ejecutar seeders necesarios para productos de conciertos
 * 
 * Uso:
 * php ejecutar_seeders_conciertos.php empresap
 * 
 * O para otro tenant:
 * php ejecutar_seeders_conciertos.php testempresa
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;

// Obtener el tenant desde los argumentos
$tenantId = $argv[1] ?? null;

if (!$tenantId) {
    echo "❌ Error: Debes especificar un tenant ID\n";
    echo "Uso: php ejecutar_seeders_conciertos.php <tenant_id>\n";
    echo "Ejemplo: php ejecutar_seeders_conciertos.php empresap\n";
    echo "\nTenants disponibles (basado en storage):\n";
    echo "  - empresap\n";
    echo "  - testempresa\n";
    echo "  - empresa1\n";
    echo "  - chuzo-de-ivan\n";
    exit(1);
}

echo "========================================\n";
echo "🎵 EJECUTANDO SEEDERS PARA CONCIERTOS\n";
echo "========================================\n";
echo "Tenant: {$tenantId}\n\n";

// Seeders necesarios para productos de conciertos (en orden)
$seeders = [
    // 1. Configuración base (obligatorio)
    ['name' => 'DepartmentSeeder', 'desc' => 'Departamentos'],
    ['name' => 'CitySeeder', 'desc' => 'Ciudades'],
    ['name' => 'CurrencySeeder', 'desc' => 'Monedas'],
    ['name' => 'InvoiceProviderSeeder', 'desc' => 'Proveedores de facturación'],
    ['name' => 'TributeSeeder', 'desc' => 'Tributos'],
    ['name' => 'TaxRateSeeder', 'desc' => 'Tasas de impuestos'],
    ['name' => 'PaymentMethodSeeder', 'desc' => 'Métodos de pago'],
    ['name' => 'IdentificationDocumentSeeder', 'desc' => 'Documentos de identificación'],
    
    // 2. Permisos y roles (obligatorio)
    ['name' => 'PermissionSeeder', 'desc' => 'Permisos'],
    ['name' => 'RoleSeeder', 'desc' => 'Roles'],
    ['name' => 'ModuleSeeder', 'desc' => 'Módulos'],
    
    // 3. Empresa y usuarios (obligatorio)
    ['name' => 'CompanySeeder', 'desc' => 'Empresa'],
    ['name' => 'UserSeeder', 'desc' => 'Usuarios'],
    
    // 4. Productos y categorías (para conciertos)
    ['name' => 'CategorySeeder', 'desc' => 'Categorías básicas'],
    ['name' => 'ProductosConciertosSeeder', 'desc' => 'Productos de conciertos ⭐'],
    
    // 5. Clientes (opcional pero recomendado)
    ['name' => 'CustomerSeeder', 'desc' => 'Clientes'],
    
    // 6. Terminales y configuración (para POS)
    ['name' => 'NumberingRangeSeeder', 'desc' => 'Rangos de numeración'],
    ['name' => 'TerminalSeeder', 'desc' => 'Terminales'],
];

$total = count($seeders);
$success = 0;
$errors = 0;
$skipped = 0;

foreach ($seeders as $index => $seeder) {
    $current = $index + 1;
    echo "[{$current}/{$total}] {$seeder['desc']} ({$seeder['name']})...\n";
    
    try {
        $command = "tenants:seed --class={$seeder['name']} --tenants={$tenantId}";
        Artisan::call($command);
        
        $output = Artisan::output();
        
        // Verificar si hay errores en la salida
        if (stripos($output, 'error') !== false || stripos($output, 'exception') !== false) {
            echo "  ⚠️  Advertencia en {$seeder['name']}\n";
            echo "  Salida: " . trim($output) . "\n";
            $errors++;
        } else {
            echo "  ✅ {$seeder['desc']} ejecutado correctamente\n";
            $success++;
        }
    } catch (\Exception $e) {
        $errorMsg = $e->getMessage();
        
        // Si es error de conexión, informar pero continuar
        if (strpos($errorMsg, 'getaddrinfo') !== false || strpos($errorMsg, 'Host desconocido') !== false) {
            echo "  ❌ Error de conexión a la base de datos\n";
            echo "  💡 Asegúrate de que MySQL esté corriendo y la configuración sea correcta\n";
            echo "\n";
            echo "========================================\n";
            echo "⚠️  NO SE PUDO CONECTAR A LA BASE DE DATOS\n";
            echo "========================================\n";
            echo "Por favor:\n";
            echo "1. Verifica que MySQL esté corriendo\n";
            echo "2. Revisa la configuración en .env (DB_HOST, DB_DATABASE, etc.)\n";
            echo "3. Si usas Docker, ejecuta: docker-compose up -d\n";
            echo "\n";
            exit(1);
        }
        
        // Si el seeder ya fue ejecutado o hay datos duplicados, continuar
        if (strpos($errorMsg, 'Duplicate') !== false || strpos($errorMsg, 'already exists') !== false) {
            echo "  ⚠️  Datos ya existen (esto es normal si se ejecuta varias veces)\n";
            $skipped++;
        } else {
            echo "  ❌ Error en {$seeder['name']}: " . $errorMsg . "\n";
            $errors++;
        }
    }
    
    echo "\n";
}

echo "========================================\n";
echo "📊 RESUMEN\n";
echo "========================================\n";
echo "✅ Exitosos: {$success}\n";
if ($skipped > 0) {
    echo "⚠️  Omitidos (ya existían): {$skipped}\n";
}
if ($errors > 0) {
    echo "❌ Errores: {$errors}\n";
}
echo "📦 Total: {$total}\n\n";

if ($errors === 0) {
    echo "🎉 ¡Todos los seeders se ejecutaron correctamente!\n";
    echo "🎵 Ya puedes probar el POS con productos de conciertos.\n";
    echo "\n";
    echo "📦 Productos creados:\n";
    echo "  - Bebidas Alcohólicas (Cerveza, Ron, Whisky, Vodka, etc.)\n";
    echo "  - Bebidas No Alcohólicas (Agua, Gaseosas, Jugos, etc.)\n";
    echo "  - Comida Rápida (Hamburguesas, Hot Dogs, Papas, etc.)\n";
    echo "  - Snacks y Dulces (Maní, Papas, Palomitas, etc.)\n";
    echo "  - Cigarrillos y Tabaco\n";
    echo "  - Merchandising (Camisetas, Gorras, Posters, etc.)\n";
    echo "  - Combos\n";
} else {
    echo "⚠️  Algunos seeders tuvieron errores. Revisa los mensajes arriba.\n";
}

