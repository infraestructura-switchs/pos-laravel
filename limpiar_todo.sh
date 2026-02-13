#!/bin/bash

# ====================================================================
# Script para Limpiar TODO - WSL/Linux
# ====================================================================
# Uso: ./limpiar_todo.sh
# ====================================================================

echo ""
echo "====================================================================="
echo "  LIMPIEZA COMPLETA - APP POS LARAVEL"
echo "====================================================================="
echo ""

echo "Limpiando caches, configuracion, rutas y vistas..."
echo ""

# Limpiar cache de aplicacion
echo "1. Cache de aplicacion..."
docker compose -f docker-compose.nginx.yml exec php php artisan cache:clear
echo "   [OK] Cache limpiado"
echo ""

# Limpiar cache de configuracion
echo "2. Cache de configuracion..."
docker compose -f docker-compose.nginx.yml exec php php artisan config:clear
echo "   [OK] Config limpiado"
echo ""

# Limpiar cache de rutas
echo "3. Cache de rutas..."
docker compose -f docker-compose.nginx.yml exec php php artisan route:clear
echo "   [OK] Rutas limpiadas"
echo ""

# Limpiar cache de vistas
echo "4. Cache de vistas..."
docker compose -f docker-compose.nginx.yml exec php php artisan view:clear
echo "   [OK] Vistas limpiadas"
echo ""

# Limpiar OPcache
echo "5. OPcache..."
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear
echo "   [OK] OPcache limpiado"
echo ""

echo "====================================================================="
echo "  LIMPIEZA COMPLETADA"
echo "====================================================================="
echo ""
echo "Si necesitas optimizar de nuevo ejecuta: ./optimizar.sh optimize"
echo ""

