<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "\n=== VERIFICACION DE TENANTS ===\n\n";

// Contar tenants
$count = Tenant::count();
echo "Total de tenants: " . $count . "\n\n";

if ($count > 0) {
    echo "Lista de tenants:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach (Tenant::all() as $tenant) {
        echo "ID: " . $tenant->id . "\n";
        echo "Nombre: " . $tenant->name . "\n";
        echo "Email: " . $tenant->email . "\n";
        echo "Estado: " . $tenant->status . "\n";
        echo "Base de datos: tenant" . $tenant->id . "\n";
        echo str_repeat("-", 60) . "\n";
    }
} else {
    echo "No hay tenants en el sistema\n";
}

// Verificar bases de datos
echo "\n=== BASES DE DATOS ===\n\n";
$databases = DB::select('SHOW DATABASES');

$tenantDatabases = [];
foreach ($databases as $db) {
    if (strpos($db->Database, 'tenant') === 0) {
        $tenantDatabases[] = $db->Database;
    }
}

if (count($tenantDatabases) > 0) {
    echo "Bases de datos de tenants encontradas:\n";
    foreach ($tenantDatabases as $db) {
        echo "  - " . $db . "\n";
    }
} else {
    echo "No hay bases de datos de tenants\n";
}

echo "\n";

