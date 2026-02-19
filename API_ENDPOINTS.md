# API Endpoints - Control de Gastos Cáceres

## Información General

- **Base URL**: `http://localhost:8000/api/v1`
- **Autenticación**: Bearer Token (Laravel Sanctum)
- **Content-Type**: `application/json`
- **Accept**: `application/json`

## Tabla de Contenidos

1. [Autenticación](#autenticación)
2. [Dashboard](#dashboard)
3. [Gastos (Expenses)](#gastos-expenses)
4. [Categorías](#categorías)
5. [Subcategorías](#subcategorías)
6. [Tarjetas](#tarjetas)
7. [Métodos de Pago](#métodos-de-pago)
8. [Comercios](#comercios)
9. [Monedas](#monedas)
10. [Tasas de Cambio](#tasas-de-cambio)
11. [Reportes](#reportes)

---

## Autenticación

### Login
```http
POST /api/v1/login
```

**Body:**
```json
{
  "email": "usuario@example.com",
  "password": "password123"
}
```

**Respuesta exitosa (200):**
```json
{
  "message": "Login exitoso",
  "user": {
    "id": 1,
    "name": "Usuario",
    "email": "usuario@example.com"
  },
  "token": "1|abc123..."
}
```

### Registro
```http
POST /api/v1/register
```

**Body:**
```json
{
  "name": "Nuevo Usuario",
  "email": "nuevo@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Respuesta exitosa (201):**
```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 2,
    "name": "Nuevo Usuario",
    "email": "nuevo@example.com"
  },
  "token": "2|xyz789..."
}
```

### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "message": "Logout exitoso"
}
```

### Obtener Usuario Autenticado
```http
GET /api/v1/me
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Usuario",
    "email": "usuario@example.com"
  }
}
```

---

## Dashboard

### Dashboard Principal
```http
GET /api/v1/dashboard
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año (ej: 2026)

**Respuesta exitosa (200):**
```json
{
  "month": "2026-02",
  "statistics": {
    "total": 15000.50,
    "count": 45,
    "by_type": {
      "fijo": { "count": 10, "total": 5000 },
      "variable": { "count": 35, "total": 10000.50 }
    }
  },
  "budget_summary": [
    {
      "category": "Alimentación",
      "budget": 5000,
      "spent": 4500,
      "remaining": 500,
      "percentage": 90,
      "status": "warning"
    }
  ]
}
```

### Estadísticas del Dashboard
```http
GET /api/v1/dashboard/stats
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "current_month": {
    "total": 15000.50,
    "count": 45,
    "by_type": { ... }
  },
  "previous_month": {
    "total": 14000,
    "count": 42
  },
  "comparison": {
    "total_change": 7.15,
    "count_change": 3
  },
  "card_expenses": [ ... ],
  "top_categories": [ ... ]
}
```

### Gastos Recientes
```http
GET /api/v1/dashboard/recent-expenses
Authorization: Bearer {token}
```

**Query Parameters:**
- `limit` (opcional): Número de gastos a retornar (default: 10)

**Respuesta exitosa (200):**
```json
{
  "data": [
    {
      "id": 1,
      "description": "Compra supermercado",
      "amount": 250.50,
      "expense_date": "2026-02-19",
      "category": { ... },
      "card": { ... }
    }
  ]
}
```

---

## Gastos (Expenses)

### Listar Gastos
```http
GET /api/v1/expenses
Authorization: Bearer {token}
```

**Query Parameters:**
- `category_id` (opcional): Filtrar por categoría
- `card_id` (opcional): Filtrar por tarjeta
- `expense_type` (opcional): fijo, variable, cuota
- `is_paid` (opcional): true/false
- `date_from` (opcional): Fecha desde (YYYY-MM-DD)
- `date_to` (opcional): Fecha hasta (YYYY-MM-DD)
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año
- `search` (opcional): Buscar en descripción
- `sort_by` (opcional): Campo para ordenar (default: expense_date)
- `sort_order` (opcional): asc/desc (default: desc)
- `per_page` (opcional): Resultados por página (default: 15)

**Respuesta exitosa (200):**
```json
{
  "data": [
    {
      "id": 1,
      "description": "Compra supermercado",
      "amount": 250.50,
      "amount_converted": 250.50,
      "expense_date": "2026-02-19",
      "expense_type": "variable",
      "is_paid": true,
      "category": {
        "id": 1,
        "name": "Alimentación"
      },
      "subcategory": {
        "id": 1,
        "name": "Supermercado"
      },
      "card": {
        "id": 1,
        "name": "Visa Débito"
      },
      "currency": {
        "id": 1,
        "code": "USD",
        "symbol": "$"
      },
      "merchant": {
        "id": 1,
        "name": "Walmart"
      },
      "payment_method": {
        "id": 1,
        "name": "Tarjeta de Débito"
      }
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

### Crear Gasto
```http
POST /api/v1/expenses
Authorization: Bearer {token}
```

**Body:**
```json
{
  "description": "Compra supermercado",
  "amount": 250.50,
  "expense_date": "2026-02-19",
  "expense_type": "variable",
  "category_id": 1,
  "subcategory_id": 1,
  "payment_method_id": 1,
  "card_id": 1,
  "currency_id": 1,
  "merchant_id": 1,
  "is_paid": true,
  "notes": "Compra mensual",
  "check_number": null,
  "installments": null,
  "current_installment": null,
  "billing_cycle": null
}
```

**Respuesta exitosa (201):**
```json
{
  "message": "Gasto creado exitosamente",
  "data": { ... }
}
```

### Ver Gasto Específico
```http
GET /api/v1/expenses/{id}
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "data": {
    "id": 1,
    "description": "Compra supermercado",
    ...
  }
}
```

### Actualizar Gasto
```http
PUT /api/v1/expenses/{id}
Authorization: Bearer {token}
```

**Body:** (mismos campos que crear)

**Respuesta exitosa (200):**
```json
{
  "message": "Gasto actualizado exitosamente",
  "data": { ... }
}
```

### Eliminar Gasto
```http
DELETE /api/v1/expenses/{id}
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "message": "Gasto eliminado exitosamente"
}
```

### Generar Cuotas
```http
POST /api/v1/expenses/{id}/generate-installments
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "message": "Cuotas generadas exitosamente",
  "data": [ ... ],
  "count": 12
}
```

### Obtener Cuotas de un Gasto
```http
GET /api/v1/expenses/{id}/installments
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "data": [ ... ],
  "total_installments": 12,
  "pending_installments": 8,
  "progress": 33.33
}
```

### Marcar como Pagado
```http
PATCH /api/v1/expenses/{id}/mark-paid
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "message": "Gasto marcado como pagado",
  "data": { ... }
}
```

### Marcar como No Pagado
```http
PATCH /api/v1/expenses/{id}/mark-unpaid
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "message": "Gasto marcado como no pagado",
  "data": { ... }
}
```

---

## Categorías

### Listar Categorías
```http
GET /api/v1/categories
Authorization: Bearer {token}
```

**Query Parameters:**
- `is_active` (opcional): true/false

**Respuesta exitosa (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Alimentación",
      "color": "#FF5733",
      "icon": "🍔",
      "monthly_budget": 5000,
      "is_active": true,
      "sort_order": 1,
      "subcategories": [ ... ]
    }
  ]
}
```

### Crear Categoría
```http
POST /api/v1/categories
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Nueva Categoría",
  "color": "#FF5733",
  "icon": "🏠",
  "monthly_budget": 3000,
  "is_active": true,
  "sort_order": 1
}
```

**Respuesta exitosa (201):**
```json
{
  "message": "Categoría creada exitosamente",
  "data": { ... }
}
```

### Ver Categoría Específica
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
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año
- `per_page` (opcional): Resultados por página

### Estado del Presupuesto
```http
GET /api/v1/categories/{id}/budget-status
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año

**Respuesta exitosa (200):**
```json
{
  "category": "Alimentación",
  "budget": 5000,
  "spent": 4500,
  "remaining": 500,
  "percentage": 90,
  "exceeded": false,
  "status": "warning",
  "month": "2026-02"
}
```

### Estadísticas de Categoría
```http
GET /api/v1/categories/{id}/statistics
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año

**Respuesta exitosa (200):**
```json
{
  "category": "Alimentación",
  "month": "2026-02",
  "total_expenses": 45,
  "total_amount": 4500,
  "average_amount": 100,
  "by_type": { ... },
  "by_subcategory": { ... }
}
```

---

## Subcategorías

### Listar Subcategorías
```http
GET /api/v1/subcategories
Authorization: Bearer {token}
```

### Crear Subcategoría
```http
POST /api/v1/subcategories
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Supermercado",
  "category_id": 1,
  "is_active": true
}
```

### Ver Subcategoría Específica
```http
GET /api/v1/subcategories/{id}
Authorization: Bearer {token}
```

### Actualizar Subcategoría
```http
PUT /api/v1/subcategories/{id}
Authorization: Bearer {token}
```

### Eliminar Subcategoría
```http
DELETE /api/v1/subcategories/{id}
Authorization: Bearer {token}
```

### Subcategorías por Categoría
```http
GET /api/v1/categories/{category_id}/subcategories
Authorization: Bearer {token}
```

---

## Tarjetas

### Listar Tarjetas
```http
GET /api/v1/cards
Authorization: Bearer {token}
```

**Query Parameters:**
- `is_active` (opcional): true/false
- `type` (opcional): credito, debito

**Respuesta exitosa (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Visa Débito",
      "type": "debito",
      "last_four_digits": "1234",
      "credit_limit": null,
      "billing_day": null,
      "payment_due_day": null,
      "is_active": true
    }
  ]
}
```

### Crear Tarjeta
```http
POST /api/v1/cards
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Visa Crédito",
  "type": "credito",
  "last_four_digits": "5678",
  "credit_limit": 10000,
  "billing_day": 15,
  "payment_due_day": 5,
  "is_active": true
}
```

### Ver Tarjeta Específica
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
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año
- `per_page` (opcional): Resultados por página

### Uso de Tarjeta
```http
GET /api/v1/cards/{id}/usage
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año

**Respuesta exitosa (200):**
```json
{
  "card": "Visa Crédito",
  "type": "credito",
  "credit_limit": 10000,
  "total_spent": 7500,
  "available": 2500,
  "usage_percentage": 75,
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

## Métodos de Pago

### Listar Métodos de Pago
```http
GET /api/v1/payment-methods
Authorization: Bearer {token}
```

### Crear Método de Pago
```http
POST /api/v1/payment-methods
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Efectivo",
  "requires_card": false,
  "is_active": true
}
```

### Ver Método de Pago Específico
```http
GET /api/v1/payment-methods/{id}
Authorization: Bearer {token}
```

### Actualizar Método de Pago
```http
PUT /api/v1/payment-methods/{id}
Authorization: Bearer {token}
```

### Eliminar Método de Pago
```http
DELETE /api/v1/payment-methods/{id}
Authorization: Bearer {token}
```

---

## Comercios

### Listar Comercios
```http
GET /api/v1/merchants
Authorization: Bearer {token}
```

### Crear Comercio
```http
POST /api/v1/merchants
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Walmart",
  "category": "Supermercado",
  "is_active": true
}
```

### Ver Comercio Específico
```http
GET /api/v1/merchants/{id}
Authorization: Bearer {token}
```

### Actualizar Comercio
```http
PUT /api/v1/merchants/{id}
Authorization: Bearer {token}
```

### Eliminar Comercio
```http
DELETE /api/v1/merchants/{id}
Authorization: Bearer {token}
```

---

## Monedas

### Listar Monedas
```http
GET /api/v1/currencies
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "data": [
    {
      "id": 1,
      "code": "USD",
      "name": "Dólar Estadounidense",
      "symbol": "$",
      "is_default": true,
      "is_active": true
    }
  ]
}
```

### Crear Moneda
```http
POST /api/v1/currencies
Authorization: Bearer {token}
```

**Body:**
```json
{
  "code": "EUR",
  "name": "Euro",
  "symbol": "€",
  "is_default": false,
  "is_active": true
}
```

### Ver Moneda Específica
```http
GET /api/v1/currencies/{id}
Authorization: Bearer {token}
```

### Actualizar Moneda
```http
PUT /api/v1/currencies/{id}
Authorization: Bearer {token}
```

### Eliminar Moneda
```http
DELETE /api/v1/currencies/{id}
Authorization: Bearer {token}
```

---

## Tasas de Cambio

### Listar Tasas de Cambio
```http
GET /api/v1/exchange-rates
Authorization: Bearer {token}
```

**Query Parameters:**
- `currency_id` (opcional): Filtrar por moneda
- `year` (opcional): Año
- `month` (opcional): Mes (1-12)
- `per_page` (opcional): Resultados por página

**Respuesta exitosa (200):**
```json
{
  "data": [
    {
      "id": 1,
      "currency_id": 2,
      "year": 2026,
      "month": 2,
      "buy_rate": 7.80,
      "sell_rate": 7.85,
      "average_rate": 7.825,
      "currency": {
        "id": 2,
        "code": "PEN",
        "name": "Sol Peruano"
      }
    }
  ]
}
```

### Crear Tasa de Cambio
```http
POST /api/v1/exchange-rates
Authorization: Bearer {token}
```

**Body:**
```json
{
  "currency_id": 2,
  "year": 2026,
  "month": 2,
  "buy_rate": 7.80,
  "sell_rate": 7.85,
  "average_rate": 7.825
}
```

### Ver Tasa de Cambio Específica
```http
GET /api/v1/exchange-rates/{id}
Authorization: Bearer {token}
```

### Actualizar Tasa de Cambio
```http
PUT /api/v1/exchange-rates/{id}
Authorization: Bearer {token}
```

### Eliminar Tasa de Cambio
```http
DELETE /api/v1/exchange-rates/{id}
Authorization: Bearer {token}
```

### Tasas por Moneda
```http
GET /api/v1/exchange-rates/currency/{currency_id}
Authorization: Bearer {token}
```

### Última Tasa de una Moneda
```http
GET /api/v1/exchange-rates/latest/{currency_id}
Authorization: Bearer {token}
```

### Convertir Moneda
```http
POST /api/v1/exchange-rates/convert
Authorization: Bearer {token}
```

**Body:**
```json
{
  "amount": 100,
  "currency_id": 2,
  "date": "2026-02-19",
  "rate_type": "average"
}
```

**Respuesta exitosa (200):**
```json
{
  "original_amount": 100,
  "converted_amount": 782.50,
  "exchange_rate": 7.825,
  "rate_type": "average",
  "date": "2026-02-19"
}
```

---

## Reportes

### Reporte Mensual
```http
GET /api/v1/reports/monthly
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año

**Respuesta exitosa (200):**
```json
{
  "period": "2026-02",
  "month_name": "febrero",
  "year": 2026,
  "statistics": {
    "total": 15000.50,
    "count": 45,
    "by_type": { ... }
  },
  "budget_summary": [ ... ]
}
```

### Reporte por Categoría
```http
GET /api/v1/reports/by-category
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año

**Respuesta exitosa (200):**
```json
{
  "period": "2026-02",
  "categories": [
    {
      "category": "Alimentación",
      "color": "#FF5733",
      "budget": 5000,
      "total": 4500,
      "count": 45,
      "average": 100,
      "percentage_of_budget": 90,
      "by_type": { ... }
    }
  ],
  "total": 15000.50
}
```

### Reporte por Tarjeta
```http
GET /api/v1/reports/by-card
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año

**Respuesta exitosa (200):**
```json
{
  "period": "2026-02",
  "cards": [
    {
      "card": "Visa Crédito",
      "type": "credito",
      "credit_limit": 10000,
      "total": 7500,
      "count": 30,
      "average": 250,
      "usage_percentage": 75,
      "by_category": { ... }
    }
  ],
  "total": 15000.50
}
```

### Reporte por Tipo de Gasto
```http
GET /api/v1/reports/by-type
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año

**Respuesta exitosa (200):**
```json
{
  "period": "2026-02",
  "by_type": [
    {
      "type": "variable",
      "count": 35,
      "total": 10000.50,
      "average": 285.73,
      "percentage": 66.67
    },
    {
      "type": "fijo",
      "count": 10,
      "total": 5000,
      "average": 500,
      "percentage": 33.33
    }
  ],
  "total": 15000.50
}
```

### Resumen de Presupuestos
```http
GET /api/v1/reports/budget-summary
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (opcional): Mes (1-12)
- `year` (opcional): Año

**Respuesta exitosa (200):**
```json
{
  "period": "2026-02",
  "summary": {
    "total_budget": 20000,
    "total_spent": 15000.50,
    "total_remaining": 4999.50,
    "usage_percentage": 75.00
  },
  "categories": [ ... ],
  "alerts": [
    {
      "category": "Alimentación",
      "status": "warning",
      "percentage": 90
    }
  ]
}
```

### Tendencias
```http
GET /api/v1/reports/trends
Authorization: Bearer {token}
```

**Query Parameters:**
- `months` (opcional): Número de meses (default: 6)

**Respuesta exitosa (200):**
```json
{
  "trends": [
    {
      "period": "2025-09",
      "month_name": "septiembre",
      "year": 2025,
      "total": 14000,
      "count": 40,
      "by_type": { ... }
    },
    ...
  ]
}
```

### Exportar Reporte
```http
POST /api/v1/reports/export
Authorization: Bearer {token}
```

**Nota:** Funcionalidad en desarrollo (retorna 501)

---

## Códigos de Estado HTTP

- `200 OK`: Solicitud exitosa
- `201 Created`: Recurso creado exitosamente
- `400 Bad Request`: Solicitud inválida
- `401 Unauthorized`: No autenticado
- `403 Forbidden`: No autorizado
- `404 Not Found`: Recurso no encontrado
- `422 Unprocessable Entity`: Error de validación
- `500 Internal Server Error`: Error del servidor
- `501 Not Implemented`: Funcionalidad no implementada

## Manejo de Errores

Todas las respuestas de error siguen este formato:

```json
{
  "message": "Descripción del error",
  "errors": {
    "campo": ["Error específico del campo"]
  }
}
```

## Paginación

Los endpoints que retornan listas incluyen información de paginación:

```json
{
  "data": [ ... ],
  "links": {
    "first": "http://...",
    "last": "http://...",
    "prev": null,
    "next": "http://..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 75
  }
}
```

## Notas Importantes

1. Todos los endpoints (excepto login y register) requieren autenticación con Bearer Token
2. Las fechas deben estar en formato ISO 8601 (YYYY-MM-DD)
3. Los montos son números decimales con hasta 2 decimales
4. Los filtros por mes/año son opcionales, por defecto usan el mes actual
5. La conversión de monedas se realiza automáticamente usando las tasas de cambio configuradas
