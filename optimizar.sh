#!/bin/bash

# ====================================================================
# Script de Optimizacion Rapida
# ====================================================================
# Uso: ./optimizar.sh [opcion]
# Opciones:
#   - test: Probar configuracion actual
#   - optimize: Optimizar aplicacion
#   - clear: Limpiar caches
#   - redis: Configurar y activar Redis
# ====================================================================

COLOR_GREEN='\033[0;32m'
COLOR_YELLOW='\033[1;33m'
COLOR_RED='\033[0;31m'
COLOR_BLUE='\033[0;34m'
COLOR_NC='\033[0m' # No Color

echo ""
echo -e "${COLOR_BLUE}=====================================================================${COLOR_NC}"
echo -e "${COLOR_BLUE}  OPTIMIZACION DE RENDIMIENTO - APP POS LARAVEL${COLOR_NC}"
echo -e "${COLOR_BLUE}=====================================================================${COLOR_NC}"
echo ""

OPTION=${1:-menu}

case $OPTION in
    test)
        echo -e "${COLOR_YELLOW}Ejecutando pruebas de rendimiento...${COLOR_NC}"
        echo ""
        docker compose -f docker-compose.nginx.yml exec php php test_performance.php
        ;;
    
    optimize)
        echo -e "${COLOR_GREEN}Optimizando aplicacion...${COLOR_NC}"
        echo ""
        docker compose -f docker-compose.nginx.yml exec php php artisan app:optimize-performance optimize
        ;;
    
    clear)
        echo -e "${COLOR_YELLOW}Limpiando caches...${COLOR_NC}"
        echo ""
        docker compose -f docker-compose.nginx.yml exec php php artisan app:optimize-performance clear
        ;;
    
    redis)
        echo -e "${COLOR_GREEN}Configurando Redis...${COLOR_NC}"
        echo ""
        echo -e "${COLOR_YELLOW}IMPORTANTE: Debes editar el archivo .env manualmente${COLOR_NC}"
        echo ""
        echo "Agrega estas lineas a tu .env:"
        echo ""
        echo -e "${COLOR_BLUE}CACHE_DRIVER=redis${COLOR_NC}"
        echo -e "${COLOR_BLUE}SESSION_DRIVER=redis${COLOR_NC}"
        echo -e "${COLOR_BLUE}REDIS_HOST=redis${COLOR_NC}"
        echo -e "${COLOR_BLUE}REDIS_PASSWORD=null${COLOR_NC}"
        echo -e "${COLOR_BLUE}REDIS_PORT=6379${COLOR_NC}"
        echo ""
        echo "Luego ejecuta:"
        echo ""
        echo -e "${COLOR_GREEN}docker compose -f docker-compose.nginx.yml restart${COLOR_NC}"
        echo -e "${COLOR_GREEN}./optimizar.sh optimize${COLOR_NC}"
        echo ""
        ;;
    
    menu|*)
        echo "Selecciona una opcion:"
        echo ""
        echo -e "  ${COLOR_GREEN}1)${COLOR_NC} Test - Probar configuracion actual"
        echo -e "  ${COLOR_GREEN}2)${COLOR_NC} Optimize - Optimizar aplicacion"
        echo -e "  ${COLOR_GREEN}3)${COLOR_NC} Clear - Limpiar caches"
        echo -e "  ${COLOR_GREEN}4)${COLOR_NC} Redis - Configurar Redis"
        echo ""
        read -p "Opcion [1-4]: " choice
        
        case $choice in
            1) bash $0 test ;;
            2) bash $0 optimize ;;
            3) bash $0 clear ;;
            4) bash $0 redis ;;
            *) echo -e "${COLOR_RED}Opcion no valida${COLOR_NC}" ;;
        esac
        ;;
esac

echo ""
echo -e "${COLOR_BLUE}=====================================================================${COLOR_NC}"
echo ""

