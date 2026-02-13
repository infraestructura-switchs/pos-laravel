<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Verificación de Dominios Centrales ===\n\n";

$centralDomains = config('tenancy.central_domains');

echo "Dominios configurados como CENTRALES:\n";
foreach ($centralDomains as $domain) {
    echo "  - {$domain}\n";
}

echo "\nDominio actual de la petición: " . request()->getHost() . "\n";

echo "\n¿Este dominio es central? ";
if (in_array(request()->getHost(), $centralDomains)) {
    echo "✅ SÍ - Usará routes/web.php\n";
} else {
    echo "❌ NO - Usará routes/tenant.php (PROBLEMA)\n";
}

echo "\n=== Fin ===\n";

