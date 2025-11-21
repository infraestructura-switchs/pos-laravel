<?php
// Script de prueba de conexión a base de datos

echo "=== Test de Conexión a Base de Datos ===\n\n";

// Cargar Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "1. Configuración del .env:\n";
echo "   DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
echo "   DB_HOST: " . env('DB_HOST') . "\n";
echo "   DB_PORT: " . env('DB_PORT') . "\n";
echo "   DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "   DB_USERNAME: " . env('DB_USERNAME') . "\n";
echo "\n";

echo "2. Probando conexión PDO directa...\n";
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
    );
    echo "   ✅ Conexión PDO exitosa\n\n";
} catch (Exception $e) {
    echo "   ❌ Error PDO: " . $e->getMessage() . "\n\n";
}

echo "3. Probando conexión de Laravel...\n";
try {
    DB::connection()->getPdo();
    echo "   ✅ Conexión Laravel exitosa\n\n";
} catch (Exception $e) {
    echo "   ❌ Error Laravel: " . $e->getMessage() . "\n\n";
}

echo "4. Probando query a usuarios...\n";
try {
    $count = DB::table('users')->count();
    echo "   ✅ Query exitoso: {$count} usuarios en la base de datos\n\n";
    
    $user = DB::table('users')->where('email', 'superadmin@gmail.com')->first();
    if ($user) {
        echo "   ✅ Usuario superadmin encontrado: {$user->name}\n";
    } else {
        echo "   ⚠️  Usuario superadmin NO encontrado\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error en query: " . $e->getMessage() . "\n\n";
}

echo "\n=== Fin del Test ===\n";

