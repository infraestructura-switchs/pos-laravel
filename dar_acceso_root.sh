#!/bin/bash

# ====================================================================
# Script para dar acceso root a admin@gmail.com
# ====================================================================
# Uso: ./dar_acceso_root.sh [nombre_base_datos]
# Ejemplo: ./dar_acceso_root.sh tenant_empresax
# ====================================================================

DB_NAME=${1:-""}

if [ -z "$DB_NAME" ]; then
    echo "Error: Debes especificar el nombre de la base de datos"
    echo "Uso: ./dar_acceso_root.sh nombre_base_datos"
    echo "Ejemplo: ./dar_acceso_root.sh tenant_empresax"
    exit 1
fi

echo ""
echo "====================================================================="
echo "  DAR ACCESO ROOT A admin@gmail.com"
echo "====================================================================="
echo ""
echo "Base de datos: $DB_NAME"
echo ""

# Ejecutar el update
docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password $DB_NAME -e "UPDATE users SET is_root = 1 WHERE email = 'admin@gmail.com';"

if [ $? -eq 0 ]; then
    echo ""
    echo "[OK] Usuario actualizado exitosamente"
    echo ""
    echo "Verificando cambios:"
    docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password $DB_NAME -e "SELECT id, name, email, is_root FROM users WHERE email = 'admin@gmail.com';"
    echo ""
    echo "====================================================================="
    echo "  COMPLETADO"
    echo "====================================================================="
    echo ""
    echo "Ahora admin@gmail.com tiene acceso a /administrador/modulos"
    echo ""
else
    echo ""
    echo "[ERROR] No se pudo actualizar el usuario"
    echo ""
fi

