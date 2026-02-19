# Solución al Error de CORS

## Problema
El login funciona en Postman pero falla en el navegador con error "Failed to fetch" o error de CORS.

## Causa
Postman no aplica políticas CORS, pero los navegadores sí. Tu frontend necesita estar en la lista de orígenes permitidos.

## Solución Implementada

### 1. Configuración actualizada en `.env`
Se agregaron más puertos comunes para desarrollo frontend:
- Puerto 8080 (común en Vue.js)
- Puertos existentes: 3000 (React), 5173 (Vite), 4200 (Angular)

### 2. Middleware CORS actualizado
Se cambió la configuración del middleware para que se aplique globalmente.

## Pasos para Probar

### 1. Limpiar caché de Laravel
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 2. Reiniciar el servidor Laravel
```bash
# Detener el servidor actual (Ctrl+C)
# Iniciar nuevamente
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. Verificar el puerto de tu frontend
Identifica en qué puerto corre tu aplicación frontend. Por ejemplo:
- React (Create React App): `http://localhost:3000`
- Vite (Vue/React): `http://localhost:5173`
- Angular: `http://localhost:4200`
- Vue CLI: `http://localhost:8080`

### 4. Agregar puerto personalizado (si es necesario)
Si tu frontend corre en un puerto diferente, agrégalo al archivo `.env`:

```env
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://localhost:5173,http://localhost:TU_PUERTO"
SANCTUM_STATEFUL_DOMAINS="localhost:3000,localhost:5173,localhost:TU_PUERTO"
```

Luego ejecuta:
```bash
php artisan config:clear
```

## Configuración del Frontend

### Headers Requeridos
Tu frontend debe enviar estos headers en cada petición:

```javascript
{
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'Authorization': 'Bearer ' + token  // Solo después del login
}
```

### Ejemplo con Fetch API
```javascript
const login = async (email, password) => {
  try {
    const response = await fetch('http://localhost:8000/api/v1/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ email, password })
    });

    if (!response.ok) {
      throw new Error('Login failed');
    }

    const data = await response.json();
    // Guardar el token
    localStorage.setItem('token', data.token);
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
};
```

### Ejemplo con Axios
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
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
const login = async (email, password) => {
  try {
    const response = await api.post('/login', { email, password });
    localStorage.setItem('token', response.data.token);
    return response.data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
};
```

### Ejemplo con React + Axios
```javascript
// src/services/api.js
import axios from 'axios';

const API_URL = 'http://localhost:8000/api/v1';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
});

// Interceptor para agregar token automáticamente
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => Promise.reject(error)
);

// Interceptor para manejar errores
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Token inválido o expirado
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export const authService = {
  login: async (email, password) => {
    const response = await api.post('/login', { email, password });
    localStorage.setItem('token', response.data.token);
    return response.data;
  },
  
  logout: async () => {
    await api.post('/logout');
    localStorage.removeItem('token');
  },
  
  me: async () => {
    const response = await api.get('/me');
    return response.data;
  }
};

export default api;
```

### Ejemplo con Vue 3 + Axios
```javascript
// src/services/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;

// src/composables/useAuth.js
import { ref } from 'vue';
import api from '@/services/api';

export function useAuth() {
  const user = ref(null);
  const token = ref(localStorage.getItem('token'));
  const loading = ref(false);
  const error = ref(null);

  const login = async (email, password) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.post('/login', { email, password });
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('token', response.data.token);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al iniciar sesión';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem('token');
    }
  };

  return {
    user,
    token,
    loading,
    error,
    login,
    logout
  };
}
```

## Verificación

### 1. Verificar que el servidor Laravel está corriendo
```bash
# Debe mostrar algo como: "Server running on [http://127.0.0.1:8000]"
php artisan serve
```

### 2. Verificar que el frontend puede conectarse
Abre la consola del navegador (F12) y verifica:
- No debe haber errores de CORS
- Las peticiones deben mostrar status 200 (éxito) o 422 (validación)
- Los headers de respuesta deben incluir `Access-Control-Allow-Origin`

### 3. Probar el endpoint de login
Desde la consola del navegador:
```javascript
fetch('http://localhost:8000/api/v1/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({
    email: 'superadmin@caceres.com.do',
    password: 'password'
  })
})
.then(res => res.json())
.then(data => console.log(data))
.catch(err => console.error(err));
```

## Problemas Comunes

### Error: "Failed to fetch"
- Verifica que el servidor Laravel esté corriendo
- Verifica que la URL sea correcta (http://localhost:8000)
- Revisa la consola del navegador para más detalles

### Error: "CORS policy"
- Ejecuta `php artisan config:clear`
- Reinicia el servidor Laravel
- Verifica que el puerto del frontend esté en `.env`

### Error: "Unauthenticated" (401)
- El token no se está enviando correctamente
- El token expiró o es inválido
- Verifica que el header `Authorization: Bearer {token}` esté presente

### Error: "The given data was invalid" (422)
- Los datos enviados no cumplen con las validaciones
- Verifica que email y password sean correctos
- Revisa el formato del JSON enviado

## Configuración de Producción

Cuando despliegues a producción, actualiza el `.env`:

```env
APP_URL=https://tu-dominio.com
CORS_ALLOWED_ORIGINS="https://tu-frontend.com"
SANCTUM_STATEFUL_DOMAINS="tu-frontend.com"
```

Y ejecuta:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Notas Importantes

1. **Nunca uses `*` en producción** para CORS_ALLOWED_ORIGINS
2. **Siempre usa HTTPS en producción**
3. **El token debe guardarse de forma segura** (localStorage o sessionStorage)
4. **Implementa refresh token** para sesiones largas
5. **Maneja la expiración del token** en el frontend
