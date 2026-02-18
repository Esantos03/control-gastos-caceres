# 🚀 Ejemplos de Peticiones API - Control de Gastos Cáceres

## 📋 Requisitos Previos

1. Servidor Laravel corriendo: `php artisan serve`
2. Base de datos con datos de prueba (ejecutar seeders)
3. URL base: `http://localhost:8000/api/v1`

---

## 🔐 PASO 1: Crear Usuario y Obtener Token

### Opción A: Registrar Nuevo Usuario

**Endpoint:** `POST /api/v1/register`

**Body (JSON):**
```json
{
  "name": "Usuario Test",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Usuario Test\",\"email\":\"test@example.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}"
```

**Respuesta:**
```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 1,
    "name": "Usuario Test",
    "email": "test@example.com",
    "created_at": "2026-02-18T10:00:00.000000Z"
  },
  "token": "1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz"
}
```

**⚠️ IMPORTANTE:** Guarda el token, lo necesitarás para todas las demás peticiones.

---

### Opción B: Login con Usuario Existente

Si ya tienes un usuario en la base de datos (por ejemplo, del seeder de Filament):

**Endpoint:** `POST /api/v1/login`

**Body (JSON):**
```json
{
  "email": "admin@admin.com",
  "password": "password"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"admin@admin.com\",\"password\":\"password\"}"
```

---

## 📊 PASO 2: Peticiones GET (Consultar Datos)

### Ejemplo 1: Obtener Información del Usuario Actual

**Endpoint:** `GET /api/v1/me`

**Headers:**
```
Authorization: Bearer {TU_TOKEN_AQUI}
Content-Type: application/json
```

**cURL:**
```bash
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json"
```

**Respuesta:**
```json
{
  "user": {
    "id": 1,
    "name": "Usuario Test",
    "email": "test@example.com",
    "created_at": "2026-02-18T10:00:00.000000Z"
  }
}
```

---

### Ejemplo 2: Listar Todas las Categorías

**Endpoint:** `GET /api/v1/categories`

**Headers:**
```
Authorization: Bearer {TU_TOKEN_AQUI}
Content-Type: application/json
```

**cURL:**
```bash
curl -X GET http://localhost:8000/api/v1/categories \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json"
```

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Alimentación",
      "color": "#FF5733",
      "icon": "shopping-cart",
      "monthly_budget": 15000,
      "sort_order": 1,
      "is_active": true,
      "subcategories": [],
      "current_month_total": 12500,
      "budget_usage_percentage": 83.33,
      "is_budget_exceeded": false,
      "created_at": "2026-02-01T10:00:00.000000Z",
      "updated_at": "2026-02-01T10:00:00.000000Z"
    }
  ]
}
```

---

### Ejemplo 3: Listar Gastos con Filtros

**Endpoint:** `GET /api/v1/expenses`

**Query Parameters:**
- `month=2` - Filtrar por mes (febrero)
- `year=2026` - Filtrar por año
- `category_id=1` - Filtrar por categoría
- `per_page=10` - Registros por página

**URL Completa:**
```
http://localhost:8000/api/v1/expenses?month=2&year=2026&per_page=10
```

**cURL:**
```bash
curl -X GET "http://localhost:8000/api/v1/expenses?month=2&year=2026&per_page=10" \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json"
```

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "expense_date": "2026-02-15",
      "description": "Compra en supermercado",
      "amount": 5000,
      "currency": {
        "id": 1,
        "code": "DOP",
        "name": "Peso Dominicano",
        "symbol": "RD$"
      },
      "exchange_rate": 1,
      "amount_converted": 5000,
      "category": {
        "id": 1,
        "name": "Alimentación"
      },
      "is_paid": true
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/v1/expenses?page=1",
    "last": "http://localhost:8000/api/v1/expenses?page=3",
    "prev": null,
    "next": "http://localhost:8000/api/v1/expenses?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 10,
    "to": 10,
    "total": 25
  }
}
```

---

### Ejemplo 4: Ver Dashboard

**Endpoint:** `GET /api/v1/dashboard`

**cURL:**
```bash
curl -X GET http://localhost:8000/api/v1/dashboard \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json"
```

---

### Ejemplo 5: Obtener Estadísticas del Dashboard

**Endpoint:** `GET /api/v1/dashboard/stats`

**cURL:**
```bash
curl -X GET http://localhost:8000/api/v1/dashboard/stats \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json"
```

---

## 📝 PASO 3: Peticiones POST (Crear Datos)

### Ejemplo 1: Crear una Categoría

**Endpoint:** `POST /api/v1/categories`

**Headers:**
```
Authorization: Bearer {TU_TOKEN_AQUI}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "name": "Transporte",
  "color": "#3498db",
  "icon": "car",
  "monthly_budget": 8000,
  "sort_order": 2,
  "is_active": true
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/categories \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Transporte\",\"color\":\"#3498db\",\"icon\":\"car\",\"monthly_budget\":8000,\"sort_order\":2,\"is_active\":true}"
```

**Respuesta:**
```json
{
  "message": "Categoría creada exitosamente",
  "data": {
    "id": 2,
    "name": "Transporte",
    "color": "#3498db",
    "icon": "car",
    "monthly_budget": 8000,
    "sort_order": 2,
    "is_active": true,
    "created_at": "2026-02-18T10:30:00.000000Z",
    "updated_at": "2026-02-18T10:30:00.000000Z"
  }
}
```

---

### Ejemplo 2: Crear un Gasto Simple

**Endpoint:** `POST /api/v1/expenses`

**Body (JSON) - Mínimo Requerido:**
```json
{
  "expense_date": "2026-02-18",
  "description": "Gasolina",
  "amount": 2500,
  "currency_id": 1,
  "category_id": 1,
  "payment_method_id": 1,
  "expense_type": "variable",
  "is_paid": true
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/expenses \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json" \
  -d "{\"expense_date\":\"2026-02-18\",\"description\":\"Gasolina\",\"amount\":2500,\"currency_id\":1,\"category_id\":1,\"payment_method_id\":1,\"expense_type\":\"variable\",\"is_paid\":true}"
```

**Respuesta:**
```json
{
  "message": "Gasto creado exitosamente",
  "data": {
    "id": 26,
    "expense_date": "2026-02-18",
    "description": "Gasolina",
    "amount": 2500,
    "currency": {
      "id": 1,
      "code": "DOP",
      "name": "Peso Dominicano",
      "symbol": "RD$"
    },
    "exchange_rate": 1,
    "amount_converted": 2500,
    "category": {
      "id": 1,
      "name": "Alimentación"
    },
    "expense_type": "variable",
    "is_paid": true,
    "created_at": "2026-02-18T10:35:00.000000Z"
  }
}
```

---

### Ejemplo 3: Crear un Gasto Completo con Todos los Campos

**Endpoint:** `POST /api/v1/expenses`

**Body (JSON) - Completo:**
```json
{
  "expense_date": "2026-02-18",
  "description": "Compra en Amazon",
  "amount": 150,
  "currency_id": 2,
  "category_id": 1,
  "subcategory_id": 1,
  "payment_method_id": 1,
  "card_id": 1,
  "merchant_id": 1,
  "expense_type": "ocasional",
  "installments": 1,
  "current_installment": 1,
  "notes": "Compra de libros",
  "is_paid": false
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/expenses \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json" \
  -d @- << 'EOF'
{
  "expense_date": "2026-02-18",
  "description": "Compra en Amazon",
  "amount": 150,
  "currency_id": 2,
  "category_id": 1,
  "subcategory_id": 1,
  "payment_method_id": 1,
  "card_id": 1,
  "merchant_id": 1,
  "expense_type": "ocasional",
  "installments": 1,
  "current_installment": 1,
  "notes": "Compra de libros",
  "is_paid": false
}
EOF
```

**Nota:** El sistema calculará automáticamente `exchange_rate` y `amount_converted` usando las tasas de cambio del mes.

---

### Ejemplo 4: Crear un Gasto con Cuotas

**Endpoint:** `POST /api/v1/expenses`

**Body (JSON):**
```json
{
  "expense_date": "2026-02-18",
  "description": "Laptop nueva",
  "amount": 30000,
  "currency_id": 1,
  "category_id": 3,
  "payment_method_id": 1,
  "card_id": 1,
  "expense_type": "ocasional",
  "installments": 12,
  "current_installment": 1,
  "is_paid": false
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/expenses \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json" \
  -d "{\"expense_date\":\"2026-02-18\",\"description\":\"Laptop nueva\",\"amount\":30000,\"currency_id\":1,\"category_id\":3,\"payment_method_id\":1,\"card_id\":1,\"expense_type\":\"ocasional\",\"installments\":12,\"current_installment\":1,\"is_paid\":false}"
```

**Luego generar las cuotas automáticamente:**

**Endpoint:** `POST /api/v1/expenses/{id}/generate-installments`

```bash
curl -X POST http://localhost:8000/api/v1/expenses/27/generate-installments \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json"
```

---

### Ejemplo 5: Crear una Tarjeta

**Endpoint:** `POST /api/v1/cards`

**Body (JSON):**
```json
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

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/cards \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Visa Gold\",\"last_digits\":\"1234\",\"type\":\"credito\",\"expiration_date\":\"2028-12-31\",\"credit_limit\":100000,\"billing_day\":15,\"payment_day\":25,\"is_active\":true,\"notes\":\"Tarjeta principal\"}"
```

---

### Ejemplo 6: Crear una Tasa de Cambio

**Endpoint:** `POST /api/v1/exchange-rates`

**Body (JSON):**
```json
{
  "currency_id": 2,
  "month": 2,
  "year": 2026,
  "buy_rate": 58.50,
  "sell_rate": 59.00,
  "average_rate": 58.75
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/exchange-rates \
  -H "Authorization: Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz" \
  -H "Content-Type: application/json" \
  -d "{\"currency_id\":2,\"month\":2,\"year\":2026,\"buy_rate\":58.50,\"sell_rate\":59.00,\"average_rate\":58.75}"
```

---

## 🧪 Probar con Postman

### Configuración Rápida:

1. **Importar Colección:**
   - Abre Postman
   - Click en "Import"
   - Selecciona el archivo `postman_collection.json`

2. **Configurar Variables:**
   - `base_url`: `http://localhost:8000/api/v1`
   - `token`: (se llenará automáticamente después del login)

3. **Hacer Login:**
   - Ejecuta el request "Auth > Login"
   - El token se guardará automáticamente

4. **Probar Endpoints:**
   - Todos los demás requests usarán el token automáticamente

---

## 🔍 Verificar que Todo Funciona

### Test Rápido Completo:

```bash
# 1. Registrar usuario
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","password":"password123","password_confirmation":"password123"}'

# 2. Guardar el token de la respuesta en una variable
TOKEN="TU_TOKEN_AQUI"

# 3. Ver perfil
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer $TOKEN"

# 4. Listar categorías
curl -X GET http://localhost:8000/api/v1/categories \
  -H "Authorization: Bearer $TOKEN"

# 5. Crear un gasto
curl -X POST http://localhost:8000/api/v1/expenses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"expense_date":"2026-02-18","description":"Test","amount":1000,"currency_id":1,"category_id":1,"payment_method_id":1,"expense_type":"variable","is_paid":false}'
```

---

## ⚠️ Errores Comunes

### Error 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```
**Solución:** Verifica que el token esté en el header `Authorization: Bearer {token}`

### Error 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```
**Solución:** Revisa que todos los campos requeridos estén presentes

### Error 404 Not Found
```json
{
  "message": "Not Found"
}
```
**Solución:** Verifica la URL del endpoint

---

## 📚 Recursos Adicionales

- **Documentación Completa:** Ver `API_DOCUMENTATION.md`
- **Guía de Instalación:** Ver `API_SETUP.md`
- **Colección Postman:** Importar `postman_collection.json`

---

**¡La API está lista para usar!** 🎉
