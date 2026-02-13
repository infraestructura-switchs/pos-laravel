# Scripts de Utilidad

## Scripts Principales

### optimizar.ps1 / optimizar.sh
Optimiza la aplicacion (cachea config, rutas, vistas).

**Uso**:
```powershell
# Windows
.\optimizar.ps1 optimize

# Linux/WSL
./optimizar.sh optimize
```

**Opciones**:
- `optimize` - Optimizar aplicacion
- `clear` - Limpiar caches
- `test` - Probar configuracion
- `redis` - Instrucciones para configurar Redis

### limpiar_todo.ps1 / limpiar_todo.sh
Limpia TODO (cache, config, rutas, vistas, OPcache).

**Uso**:
```powershell
# Windows
.\limpiar_todo.ps1

# Linux/WSL
./limpiar_todo.sh
```

### dar_acceso_root.ps1 / dar_acceso_root.sh
Da acceso de SuperAdmin a un usuario.

**Uso**:
```powershell
# Windows
.\dar_acceso_root.ps1 nombre_base_datos

# Linux/WSL
./dar_acceso_root.sh nombre_base_datos
```

## Notas

- Todos los scripts usan `docker compose` (con espacio)
- SuperAdmin (is_root = 1) tiene acceso a TODOS los modulos
- Despues de cambios en codigo PHP, ejecutar `limpiar_todo` o reiniciar PHP

## Ver bases de datos disponibles

```powershell
docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password -e "SHOW DATABASES LIKE 'tenant%';"
```

