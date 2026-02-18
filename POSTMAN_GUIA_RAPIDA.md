# 🚀 Guía Rápida - Postman

## ⚠️ Error Común: "Invalid protocol"

Si ves el error `Error: Invalid protocol: post http:`, es porque la URL incluye el método.

**❌ INCORRECTO:**
```
POST http://localhost:8000/api/v1/login
```

**✅ CORRECTO:**
- **Método (dropdown):** `POST`
- **URL (campo):** `http://localhost:8000/api/v1/login`

---

## 📝 Configuración Paso a Paso

### 1. Registrar Usuario (Primera vez)

**Configuración en Postman:**

1. **Método:** `POST` (seleccionar del dropdown)
2. **URL:** `http://localhost:8000/api/v1/register`
3. **Headers:**
   - Click en tab "Headers"
   - Agregar: `Content-Type` = `application/json`
4. **Body:**
   - Click en tab "Body"
   - Seleccionar radio button "raw"
   - En el dropdown de la derecha seleccionar "JSON"
   - Pegar este JSON:

```json
{
  "name": "Usuario Test",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

5. **Click en "Send"**

**Respuesta esperada (200):**
```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 1,
    "name": "Usuario Test",
    "email": "test@example.com",
    "created_at": "2026-02-18T10:00:00.000000Z"
  },
  "token": "1|abc123def456ghi789..."
}
```

**⚠️ IMPORTANTE:** Copia y guarda el `token` de la respuesta.

---

### 2. Login (Si ya tienes usuario)

**Configuración en Postman:**

1. **Método:** `POST`
2. **URL:** `http://localhost:8000/api/v1/login`
3. **Headers:** `Content-Type: application/json`
4. **Body (raw JSON):**

```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

5. **Click en "Send"**

---

### 3. Usar el Token en Otras Peticiones

Para todas las demás peticiones necesitas el token:

**Configuración en Postman:**

1. **Método:** `GET` o `POST` (según el endpoint)
2. **URL:** `http://localhost:8000/api/v1/...`
3. **Authorization:**
   - Click en tab "Authorization"
   - Type: Seleccionar "Bearer Token"
   - Token: Pegar tu token (sin "Bearer", solo el token)
4. **Headers:** `Content-Type: application/json`

---

## 📋 Ejemplos de Peticiones

### GET - Ver Perfil

1. **Método:** `GET`
2. **URL:** `http://localhost:8000/api/v1/me`
3. **Authorization:** Bearer Token = `tu_token_aqui`
4. **Click "Send"**

---

### GET - Listar Categorías

1. **Método:** `GET`
2. **URL:** `http://localhost:8000/api/v1/categories`
3. **Authorization:** Bearer Token = `tu_token_aqui`
4. **Click "Send"**

---

### GET - Listar Gastos con Filtros

1. **Método:** `GET`
2. **URL:** `http://localhost:8000/api/v1/expenses?month=2&year=2026&per_page=10`
3. **Authorization:** Bearer Token = `tu_token_aqui`
4. **Click "Send"**

---

### POST - Crear Categoría

1. **Método:** `POST`
2. **URL:** `http://localhost:8000/api/v1/categories`
3. **Authorization:** Bearer Token = `tu_token_aqui`
4. **Headers:** `Content-Type: application/json`
5. **Body (raw JSON):**

```json
{
  "name": "Transporte",
  "color": "#3498db",
  "icon": "car",
  "monthly_budget": 8000,
  "is_active": true
}
```

6. **Click "Send"**

---

### POST - Crear Gasto

1. **Método:** `POST`
2. **URL:** `http://localhost:8000/api/v1/expenses`
3. **Authorization:** Bearer Token = `tu_token_aqui`
4. **Headers:** `Content-Type: application/json`
5. **Body (raw JSON):**

```json
{
  "expense_date": "2026-02-18",
  "description": "Compra en supermercado",
  "amount": 5000,
  "currency_id": 1,
  "category_id": 1,
  "payment_method_id": 1,
  "expense_type": "variable",
  "is_paid": true
}
```

6. **Click "Send"**

---

## 🔧 Solución de Problemas

### Error: "Could not send request"
**Causa:** URL mal formateada  
**Solución:** Asegúrate de que la URL NO incluya el método (POST, GET, etc.)

### Error: "Unauthenticated" (401)
**Causa:** Token no válido o no enviado  
**Solución:** 
1. Verifica que el token esté en Authorization > Bearer Token
2. Haz login nuevamente para obtener un nuevo token

### Error: "The given data was invalid" (422)
**Causa:** Datos de validación incorrectos  
**Solución:** Revisa que todos los campos requeridos estén presentes y sean válidos

### Error: "SQLSTATE[23000]: Integrity constraint violation"
**Causa:** Intentas crear un gasto pero no existen las categorías/monedas/métodos de pago  
**Solución:** Primero crea las categorías, monedas y métodos de pago necesarios

---

## 📦 Datos Necesarios Antes de Crear Gastos

Antes de crear gastos, necesitas tener estos datos en la base de datos:

### 1. Crear Moneda (Currency)
```json
POST http://localhost:8000/api/v1/currencies
{
  "code": "DOP",
  "name": "Peso Dominicano",
  "symbol": "RD$"
}
```

### 2. Crear Categoría
```json
POST http://localhost:8000/api/v1/categories
{
  "name": "Alimentación",
  "color": "#FF5733",
  "monthly_budget": 15000,
  "is_active": true
}
```

### 3. Crear Método de Pago
```json
POST http://localhost:8000/api/v1/payment-methods
{
  "name": "Efectivo"
}
```

### 4. Ahora sí, Crear Gasto
```json
POST http://localhost:8000/api/v1/expenses
{
  "expense_date": "2026-02-18",
  "description": "Compra en supermercado",
  "amount": 5000,
  "currency_id": 1,
  "category_id": 1,
  "payment_method_id": 1,
  "expense_type": "variable",
  "is_paid": true
}
```

---

## 🎯 Checklist Rápido

- [ ] Servidor Laravel corriendo (`php artisan serve`)
- [ ] Usuario registrado o login exitoso
- [ ] Token guardado
- [ ] Método correcto seleccionado (GET/POST)
- [ ] URL sin el método en el texto
- [ ] Authorization configurado (Bearer Token)
- [ ] Content-Type: application/json en Headers
- [ ] Body en formato JSON (para POST)
- [ ] Datos básicos creados (monedas, categorías, métodos de pago)

---

**¡Listo para usar la API!** 🎉
