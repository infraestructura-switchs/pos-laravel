<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;

// Obtener el dominio central de la configuración
$centralDomain = config('app.central_domain', env('CENTRAL_DOMAIN', 'dokploy.movete.cloud'));

echo "========================================\n";
echo "AGREGANDO DOMINIOS A TENANTS\n";
echo "========================================\n";
echo "Dominio central: {$centralDomain}\n";
echo "========================================\n\n";

// Obtener todos los tenants
$tenants = Tenant::all();

if ($tenants->count() === 0) {
    echo "No hay tenants para procesar\n";
    exit;
}

foreach ($tenants as $tenant) {
    echo "Procesando: {$tenant->id} ({$tenant->name})\n";
    
    // Verificar si ya tiene dominio
    $existingDomains = $tenant->domains()->count();
    
    if ($existingDomains > 0) {
        echo "  ✅ Ya tiene dominio(s): ";
        foreach ($tenant->domains as $domain) {
            echo $domain->domain . " ";
        }
        echo "\n\n";
        continue;
    }
    
    // Crear el dominio
    $domainName = $tenant->id . '.' . $centralDomain;
    
    try {
        $tenant->domains()->create([
            'domain' => $domainName,
        ]);
        echo "  ✅ Dominio creado: {$domainName}\n\n";
    } catch (Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "========================================\n";
echo "✅ PROCESO COMPLETADO\n";
echo "========================================\n\n";

echo "Ahora agrega estos dominios al archivo hosts:\n\n";
foreach ($tenants as $tenant) {
    $domainName = $tenant->id . '.' . $centralDomain;
    echo "127.0.0.1    {$domainName}\n";
}

