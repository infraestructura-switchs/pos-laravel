# Configuración de API REST para Laravel POS

## Descripción

Se ha implementado una API REST completa para tu aplicación Laravel POS que permite consumir todos los endpoints desde aplicaciones externas. La API incluye autenticación con Laravel Sanctum, validación de datos, y respuestas JSON consistentes.

## Características Implementadas

### ✅ Autenticación
- Login/Logout con tokens
- Registro de usuarios
- Middleware de autenticación

### ✅ Endpoints Principales
- **Dashboard**: Estadísticas, alertas, resumen de ventas
- **Clientes**: CRUD completo con búsqueda y paginación
- **Productos**: CRUD completo con filtros por categoría
- **Facturas**: CRUD completo con gestión de detalles
- **Empresa**: Información y configuración

### ✅ Funcionalidades
- Validación de datos
- Respuestas JSON consistentes
- Paginación automática
- Filtros y búsqueda
- Manejo de errores
- CORS configurado

## Archivos Creados/Modificados

### Controladores API
- `app/Http/Controllers/Api/AuthController.php` - Autenticación
- `app/Http/Controllers/Api/CustomerController.php` - Gestión de clientes
- `app/Http/Controllers/Api/ProductController.php` - Gestión de productos
- `app/Http/Controllers/Api/BillController.php` - Gestión de facturas
- `app/Http/Controllers/Api/DashboardController.php` - Dashboard y estadísticas

### Middleware
- `app/Http/Middleware/ApiResponseMiddleware.php` - Manejo de respuestas API

### Rutas
- `routes/api.php` - Todas las rutas de la API

### Documentación
- `API_DOCUMENTATION.md` - Documentación completa de la API
- `API_SETUP_README.md` - Este archivo

## Estructura de URLs

### Base URL
```
http://tu-dominio.com/api
```

### Endpoints Principales

#### Autenticación
- `POST /auth/login` - Iniciar sesión
- `POST /auth/register` - Registrar usuario
- `POST /auth/logout` - Cerrar sesión
- `GET /auth/me` - Información del usuario

#### Dashboard
- `GET /dashboard/statistics` - Estadísticas generales
- `GET /dashboard/alerts` - Alertas del sistema
- `GET /dashboard/sales-summary` - Resumen de ventas

#### Clientes
- `GET /customers` - Listar clientes
- `GET /customers/{id}` - Obtener cliente
- `POST /customers` - Crear cliente
- `PUT /customers/{id}` - Actualizar cliente
- `DELETE /customers/{id}` - Eliminar cliente

#### Productos
- `GET /products` - Listar productos
- `GET /products/{id}` - Obtener producto
- `POST /products` - Crear producto
- `PUT /products/{id}` - Actualizar producto
- `DELETE /products/{id}` - Eliminar producto
- `GET /products/categories` - Listar categorías

#### Facturas
- `GET /bills` - Listar facturas
- `GET /bills/{id}` - Obtener factura
- `POST /bills` - Crear factura
- `PUT /bills/{id}` - Actualizar factura
- `DELETE /bills/{id}` - Eliminar factura

## Autenticación

La API utiliza Laravel Sanctum para la autenticación. El flujo es:

1. **Login**: `POST /api/auth/login` con email y password
2. **Respuesta**: Token de acceso en formato `Bearer {token}`
3. **Uso**: Incluir el token en el header `Authorization: Bearer {token}`

### Ejemplo de Login

```javascript
const response = await fetch('/api/auth/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        email: 'admin@example.com',
        password: 'password'
    })
});

const data = await response.json();
const token = data.data.token;
```

### Ejemplo de Uso con Token

```javascript
const response = await fetch('/api/products', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
    }
});
```

## Formato de Respuestas

Todas las respuestas siguen este formato:

```json
{
    "success": true|false,
    "message": "Mensaje descriptivo",
    "data": {
        // Datos de la respuesta
    },
    "errors": {
        // Errores de validación (solo en caso de error)
    }
}
```

## Parámetros de Consulta

### Paginación
- `per_page`: Número de elementos por página (default: 15)

### Ordenamiento
- `order_by`: Campo para ordenar
- `order_direction`: `asc` o `desc`

### Búsqueda
- `search`: Término de búsqueda

### Filtros de Fecha
- `date_from`: Fecha de inicio (YYYY-MM-DD)
- `date_to`: Fecha de fin (YYYY-MM-DD)

### Ejemplo
```
GET /api/customers?search=juan&per_page=10&order_by=created_at&order_direction=desc
```

## Códigos de Respuesta

- `200`: OK - Solicitud exitosa
- `201`: Created - Recurso creado exitosamente
- `400`: Bad Request - Error en los datos enviados
- `401`: Unauthorized - No autenticado
- `403`: Forbidden - No autorizado
- `404`: Not Found - Recurso no encontrado
- `422`: Unprocessable Entity - Error de validación
- `500`: Internal Server Error - Error interno del servidor

## Configuración de Producción

### 1. Variables de Entorno

Asegúrate de configurar estas variables en tu archivo `.env`:

```env
APP_URL=http://tu-dominio.com
SANCTUM_STATEFUL_DOMAINS=tu-dominio.com
SESSION_DOMAIN=.tu-dominio.com
```

### 2. Configuración CORS

Si necesitas restringir el acceso a dominios específicos, modifica `config/cors.php`:

```php
'allowed_origins' => [
    'http://tu-frontend.com',
    'https://tu-frontend.com'
],
```

### 3. Rate Limiting

La API tiene rate limiting configurado. Puedes ajustarlo en `app/Http/Kernel.php`:

```php
'throttle:api' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
```

## Pruebas

### 1. Health Check
```bash
curl http://tu-dominio.com/api/health
```

### 2. Login
```bash
curl -X POST http://tu-dominio.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### 3. Obtener Productos
```bash
curl http://tu-dominio.com/api/products \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```

## Solución de Problemas

### Error 401 Unauthorized
- Verifica que el token sea válido
- Asegúrate de incluir el header `Authorization: Bearer {token}`

### Error 422 Validation Error
- Revisa los datos enviados
- Verifica que todos los campos requeridos estén presentes

### Error 500 Internal Server Error
- Revisa los logs de Laravel en `storage/logs/laravel.log`
- Verifica la configuración de la base de datos

### CORS Errors
- Verifica la configuración en `config/cors.php`
- Asegúrate de que el dominio esté en `allowed_origins`

## Próximos Pasos

1. **Probar todos los endpoints** usando el archivo HTML de prueba
2. **Configurar CORS** según tus necesidades
3. **Implementar rate limiting** más específico si es necesario
4. **Agregar más endpoints** según las necesidades del negocio
5. **Implementar logging** específico para la API
6. **Configurar monitoreo** de la API

## Soporte

Si tienes problemas o necesitas agregar más funcionalidades:

1. Revisa la documentación completa en `API_DOCUMENTATION.md`
2. Usa el archivo de prueba `API_TEST_EXAMPLES.html`
3. Revisa los logs de Laravel para errores específicos
4. Verifica la configuración de Sanctum y CORS

¡Tu API REST está lista para ser consumida desde aplicaciones externas! 