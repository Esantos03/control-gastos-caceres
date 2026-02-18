# 📡 API REST - Control de Gastos Cáceres

## 🔐 Autenticación

La API utiliza Laravel Sanctum para autenticación mediante tokens Bearer.

### Registro de Usuario
```http
POST /api/v1/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Respuesta:**
```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "created_at": "2026-02-18T10:00:00.000000Z"
  },
  "token": "1|abc123..."
}
```

### Login
```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Respuesta:**
```json
{
  "message": "Login exitoso",
  "user": { ... },
  "token": "2|xyz789..."
}
```

### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

### Usuario Actual
```http
GET /api/v1/me
Authorization: Bearer {token}
```

---

## 💰 Gastos (Expenses)

### Listar Gastos
```http
GET /api/v1/expenses
Authorization: Bearer {token}

Parámetros de Query:
- category_id: Filtrar por categoría
- card_id: Filtrar por tarjeta
- expense_type: fijo|variable|ocasional
- is_paid: true|false
- date_from: YYYY-MM-DD
- date_to: YYYY-MM-DD
- month: 1-12
- year: YYYY
- search: Buscar en descripción
- sort_by: Campo para ordenar (default: expense_date)
- sort_order: asc|desc (default: desc)
- per_page: Registros por página (default: 15)
```

**Ejemplo:**
```http
GET /api/v1/expenses?month=2&year=2026&category_id=1&per_page=20
```

### Crear Gasto
```http
POST /api/v1/expenses
Authorization: Bearer {token}
Content-Type: application/json

{
  "expense_date": "2026-02-18",
  "description": "Compra en supermercado",
  "amount": 5000,
  "currency_id": 1,
  "category_id": 1,
  "subcategory_id": 2,
  "payment_method_id": 1,
  "card_id": 1,
  "merchant_id": 1,
  "expense_type": "variable",
  "installments": 1,
  "is_paid": true,
  "notes": "Compra mensual"
}
```

**Nota:** Los campos `exchange_rate` y `amount_converted` se calculan automáticamente si no se proporcionan.

### Ver Gasto
```http
GET /api/v1/expenses/{id}
Authorization: Bearer {token}
```

### Actualizar Gasto
```http
PUT /api/v1/expenses/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "description": "Nueva descripción",
  "amount": 6000,
  "is_paid": true
}
```

### Eliminar Gasto
```http
DELETE /api/v1/expenses/{id}
Authorization: Bearer {token}
```

### Generar Cuotas
```http
POST /api/v1/expenses/{id}/generate-installments
Authorization: Bearer {token}
```

Genera automáticamente las cuotas restantes para un gasto con `installments > 1`.

### Obtener Cuotas
```http
GET /api/v1/expenses/{id}/installments
Authorization: Bearer {token}
```

### Marcar como Pagado/No Pagado
```http
PATCH /api/v1/expenses/{id}/mark-paid
PATCH /api/v1/expenses/{id}/mark-unpaid
Authorization: Bearer {token}
```

---

## 🏷️ Categorías

### Listar Categorías
```http
GET /api/v1/categories
Authorization: Bearer {token}

Parámetros:
- is_active: true|false
```

### Crear Categoría
```http
POST /api/v1/categories
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Alimentación",
  "color": "#FF5733",
  "icon": "shopping-cart",
  "monthly_budget": 15000,
  "sort_order": 1,
  "is_active": true
}
```

### Ver Categoría
```http
GET /api/v1/categories/{id}
Authorization: Bearer {token}
```

### Actualizar Categoría
```http
PUT /api/v1/categories/{id}
Authorization: Bearer {token}
```

### Eliminar Categoría
```http
DELETE /api/v1/categories/{id}
Authorization: Bearer {token}
```

### Gastos de una Categoría
```http
GET /api/v1/categories/{id}/expenses
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
- per_page: Registros por página
```

### Estado del Presupuesto
```http
GET /api/v1/categories/{id}/budget-status
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

**Respuesta:**
```json
{
  "category": "Alimentación",
  "budget": 15000,
  "spent": 12500,
  "remaining": 2500,
  "percentage": 83.33,
  "exceeded": false,
  "status": "caution",
  "month": "2026-02"
}
```

### Estadísticas de Categoría
```http
GET /api/v1/categories/{id}/statistics
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

---

## 💳 Tarjetas

### Listar Tarjetas
```http
GET /api/v1/cards
Authorization: Bearer {token}

Parámetros:
- is_active: true|false
- type: credito|debito
```

### Crear Tarjeta
```http
POST /api/v1/cards
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Visa Gold",
  "last_digits": "1234",
  "type": "credito",
  "expiration_date": "2028-12-31",
  "credit_limit": 100000,
  "billing_day": 15,
  "payment_day": 25,
  "is_active": true,
  "notes": "Tarjeta principal"
}
```

### Ver Tarjeta
```http
GET /api/v1/cards/{id}
Authorization: Bearer {token}
```

### Actualizar Tarjeta
```http
PUT /api/v1/cards/{id}
Authorization: Bearer {token}
```

### Eliminar Tarjeta
```http
DELETE /api/v1/cards/{id}
Authorization: Bearer {token}
```

### Gastos de una Tarjeta
```http
GET /api/v1/cards/{id}/expenses
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
- per_page: Registros por página
```

### Uso de Tarjeta
```http
GET /api/v1/cards/{id}/usage
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

**Respuesta:**
```json
{
  "card": "Visa Gold",
  "type": "credito",
  "credit_limit": 100000,
  "total_spent": 45000,
  "available": 55000,
  "usage_percentage": 45,
  "is_exceeded": false,
  "month": "2026-02"
}
```

### Activar/Desactivar Tarjeta
```http
PATCH /api/v1/cards/{id}/toggle-active
Authorization: Bearer {token}
```

---

## 📊 Dashboard

### Dashboard Principal
```http
GET /api/v1/dashboard
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

### Estadísticas
```http
GET /api/v1/dashboard/stats
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "current_month": {
    "total": 125000,
    "count": 45,
    "by_type": {
      "fijo": 50000,
      "variable": 60000,
      "ocasional": 15000
    }
  },
  "previous_month": {
    "total": 110000,
    "count": 42
  },
  "comparison": {
    "total_change": 13.64,
    "count_change": 3
  },
  "card_expenses": [...],
  "top_categories": [...]
}
```

### Gastos Recientes
```http
GET /api/v1/dashboard/recent-expenses
Authorization: Bearer {token}

Parámetros:
- limit: Número de gastos (default: 10)
```

---

## 📈 Reportes

### Reporte Mensual
```http
GET /api/v1/reports/monthly
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

### Reporte por Categoría
```http
GET /api/v1/reports/by-category
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

### Reporte por Tarjeta
```http
GET /api/v1/reports/by-card
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

### Reporte por Tipo de Gasto
```http
GET /api/v1/reports/by-type
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

### Resumen de Presupuestos
```http
GET /api/v1/reports/budget-summary
Authorization: Bearer {token}

Parámetros:
- month: 1-12
- year: YYYY
```

### Tendencias
```http
GET /api/v1/reports/trends
Authorization: Bearer {token}

Parámetros:
- months: Número de meses (default: 6)
```

---

## 💱 Tasas de Cambio

### Listar Tasas
```http
GET /api/v1/exchange-rates
Authorization: Bearer {token}

Parámetros:
- currency_id: ID de moneda
- year: YYYY
- month: 1-12
- per_page: Registros por página
```

### Crear Tasa
```http
POST /api/v1/exchange-rates
Authorization: Bearer {token}
Content-Type: application/json

{
  "currency_id": 2,
  "month": 2,
  "year": 2026,
  "buy_rate": 58.50,
  "sell_rate": 59.00,
  "average_rate": 58.75
}
```

### Ver Tasa
```http
GET /api/v1/exchange-rates/{id}
Authorization: Bearer {token}
```

### Actualizar Tasa
```http
PUT /api/v1/exchange-rates/{id}
Authorization: Bearer {token}
```

### Eliminar Tasa
```http
DELETE /api/v1/exchange-rates/{id}
Authorization: Bearer {token}
```

### Tasas por Moneda
```http
GET /api/v1/exchange-rates/currency/{currency_id}
Authorization: Bearer {token}
```

### Última Tasa
```http
GET /api/v1/exchange-rates/latest/{currency_id}
Authorization: Bearer {token}
```

### Convertir Moneda
```http
POST /api/v1/exchange-rates/convert
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 1000,
  "currency_id": 2,
  "date": "2026-02-18",
  "rate_type": "average"
}
```

**Respuesta:**
```json
{
  "original_amount": 1000,
  "converted_amount": 58750,
  "exchange_rate": 58.75,
  "rate_type": "average",
  "date": "2026-02-18"
}
```

---

## 🔧 Otros Recursos

### Subcategorías
- `GET /api/v1/subcategories` - Listar todas
- `POST /api/v1/subcategories` - Crear
- `GET /api/v1/subcategories/{id}` - Ver
- `PUT /api/v1/subcategories/{id}` - Actualizar
- `DELETE /api/v1/subcategories/{id}` - Eliminar
- `GET /api/v1/categories/{id}/subcategories` - Por categoría

### Métodos de Pago
- `GET /api/v1/payment-methods` - Listar
- `POST /api/v1/payment-methods` - Crear
- `GET /api/v1/payment-methods/{id}` - Ver
- `PUT /api/v1/payment-methods/{id}` - Actualizar
- `DELETE /api/v1/payment-methods/{id}` - Eliminar

### Comercios
- `GET /api/v1/merchants` - Listar
- `POST /api/v1/merchants` - Crear
- `GET /api/v1/merchants/{id}` - Ver
- `PUT /api/v1/merchants/{id}` - Actualizar
- `DELETE /api/v1/merchants/{id}` - Eliminar

### Monedas
- `GET /api/v1/currencies` - Listar
- `POST /api/v1/currencies` - Crear
- `GET /api/v1/currencies/{id}` - Ver
- `PUT /api/v1/currencies/{id}` - Actualizar
- `DELETE /api/v1/currencies/{id}` - Eliminar

---

## 📝 Códigos de Estado HTTP

- `200 OK` - Solicitud exitosa
- `201 Created` - Recurso creado exitosamente
- `204 No Content` - Solicitud exitosa sin contenido
- `400 Bad Request` - Solicitud inválida
- `401 Unauthorized` - No autenticado
- `403 Forbidden` - No autorizado
- `404 Not Found` - Recurso no encontrado
- `422 Unprocessable Entity` - Error de validación
- `500 Internal Server Error` - Error del servidor

---

## 🚀 Instalación y Configuración

### 1. Instalar Laravel Sanctum
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2. Configurar Sanctum

En `config/sanctum.php`, asegúrate de tener:
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),
```

### 3. Agregar Sanctum al Modelo User

En `app/Models/User.php`:
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ...
}
```

### 4. Configurar Middleware

En `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
})
```

### 5. Probar la API

```bash
# Iniciar servidor
php artisan serve

# Registrar usuario
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Usar token en requests
curl -X GET http://localhost:8000/api/v1/expenses \
  -H "Authorization: Bearer {tu-token-aqui}"
```

---

## 📚 Ejemplos de Uso

### Crear un gasto completo con cuotas
```javascript
// 1. Login
const loginResponse = await fetch('http://localhost:8000/api/v1/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password123'
  })
});
const { token } = await loginResponse.json();

// 2. Crear gasto con cuotas
const expenseResponse = await fetch('http://localhost:8000/api/v1/expenses', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  },
  body: JSON.stringify({
    expense_date: '2026-02-18',
    description: 'Laptop nueva',
    amount: 30000,
    currency_id: 1,
    category_id: 3,
    payment_method_id: 1,
    card_id: 1,
    expense_type: 'ocasional',
    installments: 12,
    current_installment: 1
  })
});
const expense = await expenseResponse.json();

// 3. Generar cuotas automáticamente
await fetch(`http://localhost:8000/api/v1/expenses/${expense.data.id}/generate-installments`, {
  method: 'POST',
  headers: { 'Authorization': `Bearer ${token}` }
});
```

---

## 🔍 Notas Importantes

1. **Conversión Automática**: Los campos `exchange_rate` y `amount_converted` se calculan automáticamente usando las tasas de cambio del mes correspondiente.

2. **Paginación**: Todos los endpoints de listado soportan paginación. La respuesta incluye metadatos de paginación.

3. **Filtros**: Los filtros son opcionales y se pueden combinar.

4. **Validaciones**: Todas las validaciones están implementadas en los FormRequests correspondientes.

5. **Relaciones**: Los recursos incluyen relaciones cargadas cuando están disponibles.

6. **Cache**: Las tasas de cambio y estadísticas de presupuesto están cacheadas para mejorar el rendimiento.

---

**Versión:** 1.0  
**Última actualización:** Febrero 2026
