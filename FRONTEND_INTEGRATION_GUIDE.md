# 🚀 Guía de Integración Frontend - Control de Gastos API

## 📋 Resumen de Cambios en el Backend

### Cambios Realizados

1. **Autenticación Stateless**: La API usa tokens Bearer (NO cookies, NO CSRF)
2. **Middleware CORS**: Configurado para aceptar peticiones desde tu frontend
3. **Sesiones**: Configuradas para NO interferir con la API
4. **Rutas API**: Todas bajo el prefijo `/api/v1/`

### Configuración Actual

```env
# Backend URL
API_URL=http://127.0.0.1:8000

# CORS habilitado para estos orígenes
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173,http://localhost:4200
```

---

## 🔐 Implementación de Autenticación

### 1. Servicio de Autenticación (JavaScript/TypeScript)

```javascript
// services/authService.js
const API_URL = 'http://127.0.0.1:8000/api/v1';

class AuthService {
  // Login
  async login(email, password) {
    const response = await fetch(`${API_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ email, password })
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Error al iniciar sesión');
    }

    const data = await response.json();
    
    // Guardar token en localStorage
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
    
    return data;
  }

  // Registro
  async register(name, email, password, passwordConfirmation) {
    const response = await fetch(`${API_URL}/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        name,
        email,
        password,
        password_confirmation: passwordConfirmation
      })
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Error al registrar usuario');
    }

    const data = await response.json();
    
    // Guardar token
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
    
    return data;
  }

  // Logout
  async logout() {
    const token = this.getToken();
    
    if (token) {
      try {
        await fetch(`${API_URL}/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          }
        });
      } catch (error) {
        console.error('Error al cerrar sesión:', error);
      }
    }
    
    // Limpiar localStorage
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }

  // Obtener usuario actual
  async getCurrentUser() {
    const token = this.getToken();
    
    if (!token) {
      throw new Error('No hay sesión activa');
    }

    const response = await fetch(`${API_URL}/me`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      }
    });

    if (!response.ok) {
      throw new Error('Error al obtener usuario');
    }

    const data = await response.json();
    localStorage.setItem('user', JSON.stringify(data.user));
    
    return data.user;
  }

  // Helpers
  getToken() {
    return localStorage.getItem('token');
  }

  getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }

  isAuthenticated() {
    return !!this.getToken();
  }
}

export default new AuthService();
```

---

## 🌐 Cliente HTTP Configurado

### 2. Cliente API con Interceptores

```javascript
// services/apiClient.js
const API_URL = 'http://127.0.0.1:8000/api/v1';

class ApiClient {
  constructor() {
    this.baseURL = API_URL;
  }

  // Método genérico para hacer peticiones
  async request(endpoint, options = {}) {
    const token = localStorage.getItem('token');
    
    const config = {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(token && { 'Authorization': `Bearer ${token}` }),
        ...options.headers,
      },
    };

    try {
      const response = await fetch(`${this.baseURL}${endpoint}`, config);
      
      // Si es 401, el token expiró
      if (response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
        throw new Error('Sesión expirada');
      }

      // Si es 422, hay errores de validación
      if (response.status === 422) {
        const error = await response.json();
        throw { validationErrors: error.errors, message: error.message };
      }

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'Error en la petición');
      }

      return await response.json();
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  }

  // Métodos HTTP
  get(endpoint, params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString ? `${endpoint}?${queryString}` : endpoint;
    return this.request(url, { method: 'GET' });
  }

  post(endpoint, data) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  put(endpoint, data) {
    return this.request(endpoint, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }

  patch(endpoint, data = {}) {
    return this.request(endpoint, {
      method: 'PATCH',
      body: JSON.stringify(data),
    });
  }

  delete(endpoint) {
    return this.request(endpoint, { method: 'DELETE' });
  }
}

export default new ApiClient();
```

---

## 💰 Servicios de Recursos

### 3. Servicio de Gastos

```javascript
// services/expenseService.js
import apiClient from './apiClient';

class ExpenseService {
  // Listar gastos con filtros
  async getExpenses(filters = {}) {
    return apiClient.get('/expenses', filters);
  }

  // Obtener un gasto
  async getExpense(id) {
    return apiClient.get(`/expenses/${id}`);
  }

  // Crear gasto
  async createExpense(expenseData) {
    return apiClient.post('/expenses', expenseData);
  }

  // Actualizar gasto
  async updateExpense(id, expenseData) {
    return apiClient.put(`/expenses/${id}`, expenseData);
  }

  // Eliminar gasto
  async deleteExpense(id) {
    return apiClient.delete(`/expenses/${id}`);
  }

  // Generar cuotas
  async generateInstallments(id) {
    return apiClient.post(`/expenses/${id}/generate-installments`);
  }

  // Obtener cuotas
  async getInstallments(id) {
    return apiClient.get(`/expenses/${id}/installments`);
  }

  // Marcar como pagado
  async markAsPaid(id) {
    return apiClient.patch(`/expenses/${id}/mark-paid`);
  }

  // Marcar como no pagado
  async markAsUnpaid(id) {
    return apiClient.patch(`/expenses/${id}/mark-unpaid`);
  }
}

export default new ExpenseService();
```

### 4. Servicio de Categorías

```javascript
// services/categoryService.js
import apiClient from './apiClient';

class CategoryService {
  async getCategories(filters = {}) {
    return apiClient.get('/categories', filters);
  }

  async getCategory(id) {
    return apiClient.get(`/categories/${id}`);
  }

  async createCategory(categoryData) {
    return apiClient.post('/categories', categoryData);
  }

  async updateCategory(id, categoryData) {
    return apiClient.put(`/categories/${id}`, categoryData);
  }

  async deleteCategory(id) {
    return apiClient.delete(`/categories/${id}`);
  }

  async getCategoryExpenses(id, filters = {}) {
    return apiClient.get(`/categories/${id}/expenses`, filters);
  }

  async getBudgetStatus(id, month, year) {
    return apiClient.get(`/categories/${id}/budget-status`, { month, year });
  }

  async getStatistics(id, month, year) {
    return apiClient.get(`/categories/${id}/statistics`, { month, year });
  }
}

export default new CategoryService();
```

### 5. Servicio de Dashboard

```javascript
// services/dashboardService.js
import apiClient from './apiClient';

class DashboardService {
  async getDashboard(month, year) {
    return apiClient.get('/dashboard', { month, year });
  }

  async getStats() {
    return apiClient.get('/dashboard/stats');
  }

  async getRecentExpenses(limit = 10) {
    return apiClient.get('/dashboard/recent-expenses', { limit });
  }
}

export default new DashboardService();
```

---

## 📊 Ejemplos de Uso en Componentes

### React Example

```jsx
// components/LoginForm.jsx
import React, { useState } from 'react';
import authService from '../services/authService';

function LoginForm() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const data = await authService.login(email, password);
      console.log('Login exitoso:', data);
      // Redirigir al dashboard
      window.location.href = '/dashboard';
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h2>Iniciar Sesión</h2>
      
      {error && <div className="error">{error}</div>}
      
      <input
        type="email"
        placeholder="Email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        required
      />
      
      <input
        type="password"
        placeholder="Contraseña"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        required
      />
      
      <button type="submit" disabled={loading}>
        {loading ? 'Cargando...' : 'Iniciar Sesión'}
      </button>
    </form>
  );
}

export default LoginForm;
```

```jsx
// components/ExpenseList.jsx
import React, { useState, useEffect } from 'react';
import expenseService from '../services/expenseService';

function ExpenseList() {
  const [expenses, setExpenses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
  });

  useEffect(() => {
    loadExpenses();
  }, [filters]);

  const loadExpenses = async () => {
    try {
      setLoading(true);
      const data = await expenseService.getExpenses(filters);
      setExpenses(data.data);
    } catch (error) {
      console.error('Error al cargar gastos:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('¿Eliminar este gasto?')) {
      try {
        await expenseService.deleteExpense(id);
        loadExpenses(); // Recargar lista
      } catch (error) {
        alert('Error al eliminar gasto');
      }
    }
  };

  if (loading) return <div>Cargando...</div>;

  return (
    <div>
      <h2>Gastos</h2>
      
      {/* Filtros */}
      <div className="filters">
        <select 
          value={filters.month} 
          onChange={(e) => setFilters({...filters, month: e.target.value})}
        >
          {[...Array(12)].map((_, i) => (
            <option key={i} value={i + 1}>
              {new Date(2000, i).toLocaleString('es', { month: 'long' })}
            </option>
          ))}
        </select>
        
        <input
          type="number"
          value={filters.year}
          onChange={(e) => setFilters({...filters, year: e.target.value})}
        />
      </div>

      {/* Lista */}
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Monto</th>
            <th>Categoría</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          {expenses.map(expense => (
            <tr key={expense.id}>
              <td>{expense.expense_date}</td>
              <td>{expense.description}</td>
              <td>${expense.amount}</td>
              <td>{expense.category?.name}</td>
              <td>
                <button onClick={() => handleDelete(expense.id)}>
                  Eliminar
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default ExpenseList;
```

### Vue Example

```vue
<!-- components/LoginForm.vue -->
<template>
  <form @submit.prevent="handleLogin">
    <h2>Iniciar Sesión</h2>
    
    <div v-if="error" class="error">{{ error }}</div>
    
    <input
      v-model="email"
      type="email"
      placeholder="Email"
      required
    />
    
    <input
      v-model="password"
      type="password"
      placeholder="Contraseña"
      required
    />
    
    <button type="submit" :disabled="loading">
      {{ loading ? 'Cargando...' : 'Iniciar Sesión' }}
    </button>
  </form>
</template>

<script>
import authService from '@/services/authService';

export default {
  data() {
    return {
      email: '',
      password: '',
      error: '',
      loading: false,
    };
  },
  methods: {
    async handleLogin() {
      this.error = '';
      this.loading = true;

      try {
        await authService.login(this.email, this.password);
        this.$router.push('/dashboard');
      } catch (err) {
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
```

### Angular Example

```typescript
// services/auth.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { tap } from 'rxjs/operators';

interface LoginResponse {
  message: string;
  user: any;
  token: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://127.0.0.1:8000/api/v1';
  private tokenSubject = new BehaviorSubject<string | null>(
    localStorage.getItem('token')
  );

  constructor(private http: HttpClient) {}

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.apiUrl}/login`, {
      email,
      password
    }).pipe(
      tap(response => {
        localStorage.setItem('token', response.token);
        localStorage.setItem('user', JSON.stringify(response.user));
        this.tokenSubject.next(response.token);
      })
    );
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout`, {}).pipe(
      tap(() => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.tokenSubject.next(null);
      })
    );
  }

  getToken(): string | null {
    return this.tokenSubject.value;
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }
}
```

```typescript
// interceptors/auth.interceptor.ts
import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler } from '@angular/common/http';
import { AuthService } from '../services/auth.service';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  constructor(private authService: AuthService) {}

  intercept(req: HttpRequest<any>, next: HttpHandler) {
    const token = this.authService.getToken();

    if (token) {
      req = req.clone({
        setHeaders: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      });
    }

    return next.handle(req);
  }
}
```

---

## 🔒 Protección de Rutas

### React Router

```jsx
// components/ProtectedRoute.jsx
import { Navigate } from 'react-router-dom';
import authService from '../services/authService';

function ProtectedRoute({ children }) {
  if (!authService.isAuthenticated()) {
    return <Navigate to="/login" replace />;
  }

  return children;
}

export default ProtectedRoute;
```

```jsx
// App.jsx
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import ProtectedRoute from './components/ProtectedRoute';
import LoginForm from './components/LoginForm';
import Dashboard from './pages/Dashboard';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginForm />} />
        <Route
          path="/dashboard"
          element={
            <ProtectedRoute>
              <Dashboard />
            </ProtectedRoute>
          }
        />
      </Routes>
    </BrowserRouter>
  );
}
```

---

## ⚠️ Manejo de Errores

```javascript
// utils/errorHandler.js
export function handleApiError(error) {
  // Errores de validación (422)
  if (error.validationErrors) {
    return {
      type: 'validation',
      errors: error.validationErrors,
      message: error.message
    };
  }

  // Error de autenticación (401)
  if (error.message === 'Sesión expirada') {
    return {
      type: 'auth',
      message: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.'
    };
  }

  // Error genérico
  return {
    type: 'error',
    message: error.message || 'Ha ocurrido un error inesperado'
  };
}
```

---

## 📝 Checklist de Implementación

- [ ] Configurar variables de entorno (API_URL)
- [ ] Implementar servicio de autenticación
- [ ] Implementar cliente HTTP con interceptores
- [ ] Crear servicios para cada recurso (expenses, categories, etc.)
- [ ] Implementar protección de rutas
- [ ] Manejar errores de validación (422)
- [ ] Manejar expiración de token (401)
- [ ] Implementar logout y limpieza de localStorage
- [ ] Probar flujo completo de autenticación
- [ ] Probar CRUD de gastos
- [ ] Implementar filtros y paginación

---

## 🎯 Puntos Clave

1. **NO uses cookies**: La API usa tokens Bearer exclusivamente
2. **NO necesitas CSRF tokens**: Solo para rutas API
3. **Guarda el token**: En localStorage o sessionStorage
4. **Incluye el token**: En cada petición con `Authorization: Bearer {token}`
5. **Maneja el 401**: Redirige al login cuando el token expire
6. **Valida errores 422**: Muestra errores de validación al usuario

---

**¿Necesitas ayuda?** Revisa la documentación completa en `API_DOCUMENTATION.md`
