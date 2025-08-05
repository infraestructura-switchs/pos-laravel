# API REST Documentation

## Base URL
```
http://tu-dominio.com/api
```

## Autenticación
La API utiliza Laravel Sanctum para la autenticación. Los tokens se envían en el header `Authorization: Bearer {token}`.

## Endpoints

### Autenticación

#### POST /api/auth/login
Iniciar sesión y obtener token de acceso.

**Parámetros:**
```json
{
    "email": "usuario@ejemplo.com",
    "password": "password123"
}
```

**Respuesta exitosa:**
```json
{
    "success": true,
    "message": "Login exitoso",
    "data": {
        "user": {
            "id": 1,
            "name": "Usuario Ejemplo",
            "email": "usuario@ejemplo.com"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

#### POST /api/auth/register
Registrar nuevo usuario.

**Parámetros:**
```json
{
    "name": "Nuevo Usuario",
    "email": "nuevo@ejemplo.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### POST /api/auth/logout
Cerrar sesión (requiere autenticación).

#### GET /api/auth/me
Obtener información del usuario autenticado.

### Dashboard

#### GET /api/dashboard/statistics
Obtener estadísticas generales del dashboard.

**Parámetros opcionales:**
- `date_from`: Fecha de inicio (YYYY-MM-DD)
- `date_to`: Fecha de fin (YYYY-MM-DD)

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "sales_stats": {
            "total_sales": 1500000,
            "total_bills": 150,
            "average_ticket": 10000,
            "sales_today": 50000,
            "bills_today": 5
        },
        "product_stats": {
            "total_products": 100,
            "low_stock_products": 5,
            "out_of_stock_products": 2
        },
        "customer_stats": {
            "total_customers": 50,
            "new_customers_this_month": 10
        },
        "payment_methods": {
            "cash": 800000,
            "card": 500000,
            "transfer": 200000
        },
        "top_products": [
            {
                "name": "Producto A",
                "total_sold": 100
            }
        ],
        "daily_sales": [
            {
                "date": "2024-01-01",
                "bills_count": 10,
                "total_sales": 100000
            }
        ]
    }
}
```

#### GET /api/dashboard/alerts
Obtener alertas del sistema.

#### GET /api/dashboard/sales-summary
Obtener resumen de ventas por período.

**Parámetros:**
- `period`: week, month, year (opcional)
- `date_from`: Fecha de inicio (YYYY-MM-DD)
- `date_to`: Fecha de fin (YYYY-MM-DD)

### Clientes

#### GET /api/customers
Obtener lista de clientes.

**Parámetros opcionales:**
- `search`: Término de búsqueda
- `order_by`: Campo para ordenar
- `order_direction`: asc, desc
- `per_page`: Número de elementos por página

#### GET /api/customers/{id}
Obtener un cliente específico.

#### POST /api/customers
Crear nuevo cliente.

**Parámetros:**
```json
{
    "names": "Juan Pérez",
    "no_identification": "12345678",
    "email": "juan@ejemplo.com",
    "phone": "3001234567",
    "address": "Calle 123 #45-67",
    "dv": "9"
}
```

#### PUT /api/customers/{id}
Actualizar cliente.

#### DELETE /api/customers/{id}
Eliminar cliente.

### Productos

#### GET /api/products
Obtener lista de productos.

**Parámetros opcionales:**
- `search`: Término de búsqueda
- `category_id`: ID de categoría
- `status`: Estado del producto
- `order_by`: Campo para ordenar
- `order_direction`: asc, desc
- `per_page`: Número de elementos por página

#### GET /api/products/{id}
Obtener un producto específico.

#### POST /api/products
Crear nuevo producto.

**Parámetros:**
```json
{
    "name": "Producto Ejemplo",
    "code": "PROD001",
    "barcode": "1234567890123",
    "description": "Descripción del producto",
    "price": 10000,
    "cost": 8000,
    "stock": 100,
    "category_id": 1,
    "presentation_id": 1,
    "status": true
}
```

#### PUT /api/products/{id}
Actualizar producto.

#### DELETE /api/products/{id}
Eliminar producto.

#### GET /api/products/categories
Obtener lista de categorías.

### Facturas

#### GET /api/bills
Obtener lista de facturas.

**Parámetros opcionales:**
- `search`: Término de búsqueda
- `date_from`: Fecha de inicio
- `date_to`: Fecha de fin
- `status`: Estado de la factura
- `order_by`: Campo para ordenar
- `order_direction`: asc, desc
- `per_page`: Número de elementos por página

#### GET /api/bills/{id}
Obtener una factura específica.

#### POST /api/bills
Crear nueva factura.

**Parámetros:**
```json
{
    "customer_id": 1,
    "numbering_range_id": 1,
    "cash": 50000,
    "card": 30000,
    "transfer": 20000,
    "details": [
        {
            "product_id": 1,
            "amount": 2,
            "price": 10000,
            "tax_rate_id": 1
        }
    ]
}
```

#### PUT /api/bills/{id}
Actualizar factura.

#### DELETE /api/bills/{id}
Eliminar factura.

#### GET /api/bills/{id}/statistics
Obtener estadísticas de una factura específica.

### Empresa

#### GET /api/company/show
Obtener información de la empresa.

#### POST /api/company/update
Actualizar información de la empresa.

### Usuario

#### GET /api/user
Obtener información del usuario autenticado.

### Health Check

#### GET /api/health
Verificar el estado de la API.

## Códigos de Respuesta

- `200`: OK - Solicitud exitosa
- `201`: Created - Recurso creado exitosamente
- `400`: Bad Request - Error en los datos enviados
- `401`: Unauthorized - No autenticado
- `403`: Forbidden - No autorizado
- `404`: Not Found - Recurso no encontrado
- `422`: Unprocessable Entity - Error de validación
- `500`: Internal Server Error - Error interno del servidor

## Formato de Respuesta

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

## Ejemplos de Uso

### JavaScript (Fetch API)

```javascript
// Login
const login = async (email, password) => {
    const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
    });
    
    return response.json();
};

// Obtener productos con token
const getProducts = async (token) => {
    const response = await fetch('/api/products', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });
    
    return response.json();
};
```

### PHP (cURL)

```php
// Login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://tu-dominio.com/api/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'usuario@ejemplo.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
```

### Python (requests)

```python
import requests

# Login
response = requests.post('http://tu-dominio.com/api/auth/login', json={
    'email': 'usuario@ejemplo.com',
    'password': 'password123'
})

data = response.json()
token = data['data']['token']

# Obtener productos
headers = {'Authorization': f'Bearer {token}'}
products_response = requests.get('http://tu-dominio.com/api/products', headers=headers)
products = products_response.json()
```

## Notas Importantes

1. **Autenticación**: Todas las rutas excepto login, register y health requieren autenticación.
2. **Rate Limiting**: La API tiene límites de velocidad configurados.
3. **Validación**: Todos los endpoints validan los datos de entrada.
4. **Paginación**: Los endpoints de listado incluyen paginación automática.
5. **Filtros**: La mayoría de endpoints de listado soportan filtros y búsqueda.
6. **Relaciones**: Los modelos incluyen sus relaciones cuando es necesario.

## Configuración CORS

Si necesitas consumir la API desde un frontend en otro dominio, asegúrate de configurar CORS en tu aplicación Laravel. 