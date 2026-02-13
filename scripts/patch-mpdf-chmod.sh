#!/bin/bash

# Script para parchear mPDF y solucionar el error de chmod en Docker/WSL
# Este script debe ejecutarse después de composer install/update

echo "🔧 Aplicando parche a mPDF para solucionar error de chmod..."

CACHE_FILE="vendor/mpdf/mpdf/src/Cache.php"

if [ ! -f "$CACHE_FILE" ]; then
    echo "❌ Error: No se encontró $CACHE_FILE"
    exit 1
fi

# Verificar si el parche ya está aplicado
if grep -q "Ignorar errores de chmod en sistemas de archivos montados" "$CACHE_FILE"; then
    echo "✅ El parche ya está aplicado"
    exit 0
fi

# Aplicar el parche
sed -i 's/@chmod($tempFile, 0664);/try {\n\t\t\t@chmod($tempFile, 0664);\n\t\t} catch (\\Throwable $e) {\n\t\t\t\/\/ Ignorar errores de chmod en sistemas de archivos montados\n\t\t}/g' "$CACHE_FILE"

if [ $? -eq 0 ]; then
    echo "✅ Parche aplicado exitosamente"
else
    echo "❌ Error al aplicar el parche"
    exit 1
fi
