<?php

// Script para asignar rol de Administrador al SuperAdmin

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "=== Asignando Rol de Administrador a SuperAdmin ===\n\n";

// Buscar el usuario superadmin
$user = User::where('email', 'superadmin@gmail.com')->first();

if (!$user) {
    echo "❌ Usuario superadmin@gmail.com no encontrado\n";
    exit(1);
}

echo "Usuario encontrado: {$user->name} ({$user->email})\n";

// Buscar el rol de Administrador
$adminRole = Role::where('name', 'Administrador')->first();

if (!$adminRole) {
    echo "❌ Rol 'Administrador' no encontrado\n";
    exit(1);
}

echo "Rol encontrado: {$adminRole->name}\n\n";

// Asignar el rol
try {
    // Primero eliminar roles existentes (si los hay)
    $user->syncRoles([]);
    
    // Asignar el rol de Administrador
    $user->assignRole($adminRole);
    
    echo "✅ Rol 'Administrador' asignado correctamente a {$user->name}\n\n";
    
    // Verificar
    echo "Roles del usuario:\n";
    foreach ($user->roles as $role) {
        echo "  - {$role->name}\n";
    }
    
    echo "\nPermisos del rol 'Administrador':\n";
    $permissions = $adminRole->permissions;
    echo "  Total de permisos: " . $permissions->count() . "\n";
    
    if ($permissions->count() > 0) {
        echo "\n  Primeros 10 permisos:\n";
        foreach ($permissions->take(10) as $permission) {
            echo "    - {$permission->name}\n";
        }
        
        if ($permissions->count() > 10) {
            echo "    ... y " . ($permissions->count() - 10) . " más\n";
        }
    } else {
        echo "  ⚠️  El rol 'Administrador' no tiene permisos asignados\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Completado ===\n";
echo "Ahora recarga la página en tu navegador (Ctrl + Shift + R)\n";

