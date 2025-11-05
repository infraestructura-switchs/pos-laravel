<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;

echo "\n";
echo "========================================\n";
echo "   ELIMINANDO TODOS LOS TENANTS        \n";
echo "========================================\n\n";

$tenants = Tenant::all();

if ($tenants->count() === 0) {
    echo "⚠️  No hay tenants para eliminar\n\n";
    exit;
}

echo "📋 Tenants encontrados: " . $tenants->count() . "\n\n";

foreach ($tenants as $tenant) {
    echo "🗑️  Eliminando: {$tenant->name}\n";
    echo "    ID: {$tenant->id}\n";
    echo "    Email: {$tenant->email}\n";
    echo "    BD: tenant{$tenant->id}\n";
    
    try {
        $tenant->delete();
        echo "    ✅ Eliminado correctamente\n\n";
    } catch (Exception $e) {
        echo "    ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "========================================\n";
echo "✅ PROCESO COMPLETADO                  \n";
echo "========================================\n\n";

// Verificar que se eliminaron
$remaining = Tenant::count();
echo "Tenants restantes: {$remaining}\n\n";

