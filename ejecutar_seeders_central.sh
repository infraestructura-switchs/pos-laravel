#!/bin/bash

# Script para ejecutar seeders en la base de datos CENTRAL (no tenants)
# La BD central contiene:
# - Usuarios super admin
# - Registro de tenants
# - Dominios

echo "========================================"
echo "🌐 EJECUTANDO SEEDERS EN BD CENTRAL"
echo "========================================"
echo ""

# Ejecutar SuperAdminSeeder
echo "🔑 Creando/Verificando Super Admin..."
wsl docker exec laravel-php-fpm php artisan db:seed --class=SuperAdminSeeder

echo ""
echo "========================================"
echo "✅ SEEDERS CENTRALES COMPLETADOS"
echo "========================================"
echo ""
echo "📋 Información de acceso:"
echo "   Dominio: http://adminpos.dokploy.movete.cloud"
echo "   Email: superadmin@gmail.com"
echo "   Password: 123456"
echo ""
echo "💡 Desde este usuario puedes:"
echo "   - Crear nuevos tenants (empresas)"
echo "   - Gestionar tenants existentes"
echo "   - Ver todos los dominios"
echo ""

