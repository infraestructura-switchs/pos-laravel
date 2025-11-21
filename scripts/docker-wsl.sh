#!/bin/bash

# Script para trabajar con Docker desde WSL2
# Uso: ./scripts/docker-wsl.sh [comando]
# Comandos: start, stop, restart, logs, status, shell

set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
COMPOSE_FILE="$PROJECT_DIR/docker-compose.nginx.yml"

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Función para mostrar ayuda
show_help() {
    echo -e "${CYAN}============================================${NC}"
    echo -e "${CYAN}  Docker Manager para WSL2${NC}"
    echo -e "${CYAN}============================================${NC}"
    echo ""
    echo "Uso: $0 [comando]"
    echo ""
    echo "Comandos disponibles:"
    echo "  start     - Iniciar contenedores"
    echo "  stop      - Detener contenedores"
    echo "  restart   - Reiniciar contenedores"
    echo "  logs      - Ver logs de los contenedores"
    echo "  status    - Ver estado de los contenedores"
    echo "  shell     - Abrir shell en el contenedor PHP"
    echo "  build     - Construir imágenes"
    echo "  down      - Detener y eliminar contenedores"
    echo "  help      - Mostrar esta ayuda"
    echo ""
}

# Verificar que docker está instalado
check_docker() {
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}✗ Docker no está instalado${NC}"
        exit 1
    fi
    
    if ! docker ps &> /dev/null; then
        echo -e "${RED}✗ Docker no está corriendo${NC}"
        echo -e "${YELLOW}Por favor inicia Docker Desktop${NC}"
        exit 1
    fi
}

# Verificar que docker-compose está instalado
check_compose() {
    if command -v docker-compose &> /dev/null; then
        COMPOSE_CMD="docker-compose"
    elif docker compose version &> /dev/null; then
        COMPOSE_CMD="docker compose"
    else
        echo -e "${RED}✗ docker-compose no encontrado${NC}"
        exit 1
    fi
}

# Cambiar al directorio del proyecto
cd "$PROJECT_DIR"

# Verificar archivo docker-compose
if [ ! -f "$COMPOSE_FILE" ]; then
    echo -e "${RED}✗ docker-compose.nginx.yml no encontrado${NC}"
    exit 1
fi

# Procesar comando
case "${1:-help}" in
    start)
        check_docker
        check_compose
        echo -e "${CYAN}Iniciando contenedores...${NC}"
        $COMPOSE_CMD -f "$COMPOSE_FILE" up -d
        echo -e "${GREEN}✓ Contenedores iniciados${NC}"
        echo ""
        $COMPOSE_CMD -f "$COMPOSE_FILE" ps
        ;;
    
    stop)
        check_docker
        check_compose
        echo -e "${CYAN}Deteniendo contenedores...${NC}"
        $COMPOSE_CMD -f "$COMPOSE_FILE" stop
        echo -e "${GREEN}✓ Contenedores detenidos${NC}"
        ;;
    
    restart)
        check_docker
        check_compose
        echo -e "${CYAN}Reiniciando contenedores...${NC}"
        $COMPOSE_CMD -f "$COMPOSE_FILE" restart
        echo -e "${GREEN}✓ Contenedores reiniciados${NC}"
        ;;
    
    logs)
        check_docker
        check_compose
        echo -e "${CYAN}Mostrando logs...${NC}"
        $COMPOSE_CMD -f "$COMPOSE_FILE" logs -f
        ;;
    
    status)
        check_docker
        check_compose
        echo -e "${CYAN}Estado de los contenedores:${NC}"
        $COMPOSE_CMD -f "$COMPOSE_FILE" ps
        ;;
    
    shell)
        check_docker
        echo -e "${CYAN}Abriendo shell en contenedor PHP...${NC}"
        docker exec -it laravel-php-fpm bash
        ;;
    
    build)
        check_docker
        check_compose
        echo -e "${CYAN}Construyendo imágenes...${NC}"
        $COMPOSE_CMD -f "$COMPOSE_FILE" build
        echo -e "${GREEN}✓ Imágenes construidas${NC}"
        ;;
    
    down)
        check_docker
        check_compose
        echo -e "${YELLOW}¿Estás seguro de que quieres detener y eliminar los contenedores? (s/n)${NC}"
        read -r response
        if [[ "$response" =~ ^[Ss]$ ]]; then
            $COMPOSE_CMD -f "$COMPOSE_FILE" down
            echo -e "${GREEN}✓ Contenedores eliminados${NC}"
        else
            echo "Operación cancelada"
        fi
        ;;
    
    help|*)
        show_help
        ;;
esac

