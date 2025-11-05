# 🗑️ GUÍA - Cómo Eliminar Tenants

(Documento movido desde la raíz)

## 📋 MÉTODOS DISPONIBLES

---

## ✅ MÉTODO 1: Panel de Administración (Recomendado)

### **Pasos:**

1. **Haz login en el dominio central:**
   ```
   http://dokploy.movete.cloud/login
   
   Email: superadmin@gmail.com
   Password: 123456
   ```

2. **Ve al panel de tenants:**
   ```
   http://dokploy.movete.cloud/admin/tenants
   ```

3. **Verás una lista de todos los tenants con botones de acción:**
   - 👁️ Ver (información detallada)
   - ✏️ Editar (modificar datos)
   - ⏸️ Suspender (deshabilitar temporalmente)
   - 🗑️ **Eliminar** (borrar permanentemente)

4. **Click en el botón rojo 🗑️ "Eliminar"**
   - Te pedirá confirmación
   - Click en "Aceptar"
   - El tenant y su base de datos se eliminarán

---

## 🔧 MÉTODO 2: Comando Artisan (Rápido)

### **Eliminar un tenant específico:**

```bash
php artisan tinker

$tenant = App\Models\Tenant::find('empresa1');
$tenant->delete();
exit
```

**Esto automáticamente:**
- ✅ Elimina el registro del tenant
- ✅ Elimina su dominio
- ✅ Elimina su base de datos

---

## 🧹 MÉTODO 3: Script para Eliminar Todos (Limpieza Total)

Crea un archivo `clean_all_tenants.php`:

```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;

echo "========================================\n";
echo "ELIMINANDO TODOS LOS TENANTS\n";
echo "========================================\n\n";

$tenants = Tenant::all();

if ($tenants->count() === 0) {
    echo "⚠️  No hay tenants para eliminar\n";
    exit;
}

echo "Tenants encontrados: " . $tenants->count() . "\n\n";

foreach ($tenants as $tenant) {
    echo "🗑️  Eliminando: {$tenant->name} (ID: {$tenant->id})\n";
    echo "   Email: {$tenant->email}\n";
    echo "   BD: tenant{$tenant->id}\n";
    
    try {
        $tenant->delete();
        echo "   ✅ Eliminado correctamente\n\n";
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "========================================\n";
echo "✅ PROCESO COMPLETADO\n";
echo "========================================\n";
```

**Ejecutar:**
```bash
php clean_all_tenants.php
```

---

## 🎯 MÉTODO 4: Eliminar Tenants Específicos

Crea un archivo `delete_tenant.php`:

```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;

// Lista de tenants a eliminar
$tenantsToDelete = [
    'empresa1',
    'empresa1-1',
    'empresa2',
    'mi-negocio',
];

echo "========================================\n";
echo "ELIMINANDO TENANTS ESPECÍFICOS\n";
echo "========================================\n\n";

foreach ($tenantsToDelete as $tenantId) {
    echo "Buscando tenant: {$tenantId}...\n";
    
    $tenant = Tenant::find($tenantId);
    
    if ($tenant) {
        echo "  Nombre: {$tenant->name}\n";
        echo "  Email: {$tenant->email}\n";
        echo "  Eliminando...\n";
        
        try {
            $tenant->delete();
            echo "  ✅ Eliminado correctamente\n\n";
        } catch (Exception $e) {
            echo "  ❌ Error: " . $e->getMessage() . "\n\n";
        }
    } else {
        echo "  ⚠️  No encontrado\n\n";
    }
}

echo "========================================\n";
echo "✅ PROCESO COMPLETADO\n";
echo "========================================\n";
```

**Ejecutar:**
```bash
php delete_tenant.php
```

---

## 📊 VERIFICAR TENANTS ANTES DE ELIMINAR

### **Ver lista de todos los tenants:**

```bash
php artisan tinker

App\Models\Tenant::all(['id', 'name', 'email', 'status'])->toArray();
exit
```

### **Contar tenants:**

```bash
php artisan tinker

echo "Total de tenants: " . App\Models\Tenant::count();
exit
```

---

## ⚠️ IMPORTANTE

### **Lo que se elimina cuando borras un tenant:**

1. ✅ Registro del tenant en tabla `tenants`
2. ✅ Dominios asociados en tabla `domains`
3. ✅ **Base de datos completa** del tenant (Ej: `tenantempresa1`)
4. ✅ Todos los datos: usuarios, productos, ventas, etc.

### **⚠️ ESTA ACCIÓN ES IRREVERSIBLE**

**No hay forma de recuperar:**
- Los datos del tenant
- Los productos
- Las ventas
- Los clientes
- Nada

---

## 🔍 VERIFICAR QUE SE ELIMINÓ

### **Ver bases de datos restantes:**

```bash
php artisan tinker

foreach(DB::select('SHOW DATABASES') as $db) {
    echo $db->Database . PHP_EOL;
}
exit
```

### **Verificar tenants restantes:**

```bash
php artisan tinker

$count = App\Models\Tenant::count();
echo "Tenants restantes: {$count}\n";

if ($count > 0) {
    foreach (App\Models\Tenant::all() as $tenant) {
        echo "  - {$tenant->id} ({$tenant->name})\n";
    }
}
exit
```

---

## 🎯 RECOMENDACIÓN

### **Para desarrollo/pruebas:**
- Usa el **Método 3** (Script para eliminar todos)
- Rápido y limpio

### **Para producción:**
- Usa el **Método 1** (Panel de administración)
- Más seguro y con confirmación

---

## 🧪 SCRIPT RÁPIDO PARA LIMPIAR TODO

```bash
# Crear el script
cat > clean_all_tenants.php << 'EOF'
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;

echo "Eliminando todos los tenants...\n";
$count = 0;

foreach (Tenant::all() as $tenant) {
    echo "Eliminando: {$tenant->id}... ";
    $tenant->delete();
    echo "✅\n";
    $count++;
}

echo "\n✅ {$count} tenants eliminados\n";
EOF

# Ejecutar
php clean_all_tenants.php

# Eliminar el script
rm clean_all_tenants.php
```

---

## ✅ DESPUÉS DE ELIMINAR

No olvides limpiar la caché:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 📝 RESUMEN RÁPIDO

| Método | Uso | Seguridad |
|--------|-----|-----------|
| Panel Admin | Producción | ⭐⭐⭐⭐⭐ |
| Artisan Tinker | Desarrollo | ⭐⭐⭐⭐ |
| Script Todos | Limpieza rápida | ⭐⭐⭐ |
| Script Específicos | Selectivo | ⭐⭐⭐⭐ |

---

¿Qué método prefieres usar? 🤔
