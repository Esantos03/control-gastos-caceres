# 🚀 Configuración de la API - Control de Gastos Cáceres

## 📋 Requisitos Previos

- PHP 8.2 o superior
- Composer
- Laravel 12
- Base de datos configurada (SQLite/MySQL/PostgreSQL)

## 🔧 Instalación Paso a Paso

### 1. Instalar Laravel Sanctum

```bash
composer require laravel/sanctum
```

### 2. Publicar Configuración de Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Ejecutar Migraciones

```bash
php artisan migrate
```

Esto creará la tabla `personal_access_tokens` necesaria para Sanctum.

### 4. Configurar Middleware API

Editar `bootstrap/app.php` y agregar el middleware de Sanctum:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        $middleware->alias([
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### 5. Configurar Variables de Entorno

Editar `.env`:

```env
# Configuración de Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000

# Si usas un frontend en otro dominio
# SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,tudominio.com

# Configuración de sesión para API
SESSION_DRIVER=cookie
SESSION_DOMAIN=localhost
```

### 6. Verificar Modelo User

El modelo `app/Models/User.php` ya debe tener el trait `HasApiTokens`:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ...
}
```

### 7. Limpiar Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## ✅ Verificar Instalación

### 1. Iniciar Servidor

```bash
php artisan serve
```

### 2. Probar Registro

```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Usuario Test",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Respuesta esperada:**
```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 1,
    "name": "Usuario Test",
    "email": "test@example.com",
    "created_at": "2026-02-18T10:00:00.000000Z"
  },
  "token": "1|abc123def456..."
}
```

### 3. Probar Login

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### 4. Probar Endpoint Protegido

```bash
# Reemplaza {TOKEN} con el token obtenido en login
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer {TOKEN}"
```

### 5. Probar Creación de Gasto

```bash
curl -X POST http://localhost:8000/api/v1/expenses \
  -H "Authorization: Bearer {TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "expense_date": "2026-02-18",
    "description": "Prueba de gasto",
    "amount": 1000,
    "currency_id": 1,
    "category_id": 1,
    "payment_method_id": 1,
    "expense_type": "variable",
    "is_paid": false
  }'
```

## 🔍 Solución de Problemas

### Error: "Unauthenticated"

**Causa:** Token no válido o no enviado correctamente.

**Solución:**
1. Verificar que el header `Authorization: Bearer {token}` esté presente
2. Verificar que el token sea válido
3. Hacer login nuevamente para obtener un nuevo token

### Error: "CSRF token mismatch"

**Causa:** Configuración incorrecta de Sanctum para SPA.

**Solución:**
1. Verificar `SANCTUM_STATEFUL_DOMAINS` en `.env`
2. Asegurarse de que el middleware esté configurado correctamente
3. Limpiar cache: `php artisan config:clear`

### Error: "Route not found"

**Causa:** Las rutas de API no están cargadas.

**Solución:**
1. Verificar que `routes/api.php` existe
2. Verificar configuración en `bootstrap/app.php`
3. Ejecutar: `php artisan route:list` para ver rutas disponibles

### Error: "Class not found"

**Causa:** Autoload no actualizado.

**Solución:**
```bash
composer dump-autoload
php artisan optimize:clear
```

### Error de Validación

**Causa:** Datos enviados no cumplen las reglas de validación.

**Solución:**
1. Revisar la documentación de la API
2. Verificar que todos los campos requeridos estén presentes
3. Verificar que los tipos de datos sean correctos

## 📊 Datos de Prueba

### Crear Usuario Admin

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('admin123')
]);

echo "Usuario creado: " . $user->email;
```

### Ejecutar Seeders

Si tienes seeders configurados:

```bash
php artisan db:seed
```

O ejecutar el seeder completo:

```bash
php artisan db:seed --class=CompleteSeeder
```

## 🧪 Testing con Postman

### 1. Importar Colección

Crear una nueva colección en Postman con las siguientes variables:

- `base_url`: `http://localhost:8000/api/v1`
- `token`: (se llenará automáticamente después del login)

### 2. Configurar Autenticación

En cada request protegido:
- Type: Bearer Token
- Token: `{{token}}`

### 3. Script de Login

En el request de login, agregar en "Tests":

```javascript
if (pm.response.code === 200) {
    const response = pm.response.json();
    pm.collectionVariables.set("token", response.token);
}
```

## 🔐 Seguridad

### Mejores Prácticas

1. **Nunca exponer tokens en logs o código**
2. **Usar HTTPS en producción**
3. **Implementar rate limiting**
4. **Validar todos los inputs**
5. **Implementar políticas de autorización**

### Rate Limiting (Opcional)

Editar `app/Http/Kernel.php` o `bootstrap/app.php`:

```php
$middleware->throttleApi();
```

Por defecto, Laravel limita a 60 requests por minuto.

### Configurar CORS (Si usas frontend separado)

```bash
composer require fruitcake/laravel-cors
```

Editar `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

## 📱 Integración con Frontend

### Ejemplo con JavaScript/Fetch

```javascript
class ApiClient {
  constructor(baseUrl) {
    this.baseUrl = baseUrl;
    this.token = localStorage.getItem('token');
  }

  async login(email, password) {
    const response = await fetch(`${this.baseUrl}/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    this.token = data.token;
    localStorage.setItem('token', data.token);
    return data;
  }

  async get(endpoint) {
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json'
      }
    });
    return response.json();
  }

  async post(endpoint, data) {
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });
    return response.json();
  }
}

// Uso
const api = new ApiClient('http://localhost:8000/api/v1');

// Login
await api.login('user@example.com', 'password123');

// Obtener gastos
const expenses = await api.get('/expenses');

// Crear gasto
const newExpense = await api.post('/expenses', {
  expense_date: '2026-02-18',
  description: 'Nuevo gasto',
  amount: 1000,
  currency_id: 1,
  category_id: 1,
  payment_method_id: 1,
  expense_type: 'variable'
});
```

### Ejemplo con Axios

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json'
  }
});

// Interceptor para agregar token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Login
const { data } = await api.post('/login', {
  email: 'user@example.com',
  password: 'password123'
});
localStorage.setItem('token', data.token);

// Obtener gastos
const expenses = await api.get('/expenses');

// Crear gasto
const newExpense = await api.post('/expenses', {
  expense_date: '2026-02-18',
  description: 'Nuevo gasto',
  amount: 1000,
  currency_id: 1,
  category_id: 1,
  payment_method_id: 1,
  expense_type: 'variable'
});
```

## 🎯 Próximos Pasos

1. ✅ API REST completamente funcional
2. ⏳ Implementar tests de API
3. ⏳ Agregar documentación OpenAPI/Swagger
4. ⏳ Implementar webhooks
5. ⏳ Agregar versionado de API (v2, v3, etc.)
6. ⏳ Implementar GraphQL (opcional)

## 📚 Recursos Adicionales

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [API Documentation](./API_DOCUMENTATION.md)
- [Project README](./README.md)

---

**¡La API está lista para usar!** 🎉

Para más información, consulta la [documentación completa de la API](./API_DOCUMENTATION.md).
