# Implementar Polling en el Frontend

## ¿Qué es Polling?

Polling es una técnica donde el frontend consulta periódicamente al servidor para obtener datos actualizados sin necesidad de refrescar la página manualmente.

## Opciones de Implementación

### Opción 1: Polling Simple (Recomendado para empezar)

Consulta el servidor cada X segundos para obtener datos actualizados.

#### React Example

```javascript
import { useState, useEffect } from 'react';
import api from './services/api';

function Dashboard() {
  const [expenses, setExpenses] = useState([]);
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  // Función para cargar datos
  const fetchData = async () => {
    try {
      const [expensesRes, statsRes] = await Promise.all([
        api.get('/expenses'),
        api.get('/dashboard/stats')
      ]);
      
      setExpenses(expensesRes.data.data);
      setStats(statsRes.data);
      setLoading(false);
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  };

  // Cargar datos inicialmente
  useEffect(() => {
    fetchData();
  }, []);

  // Polling: Actualizar cada 30 segundos
  useEffect(() => {
    const interval = setInterval(() => {
      fetchData();
    }, 30000); // 30 segundos

    // Limpiar intervalo al desmontar
    return () => clearInterval(interval);
  }, []);

  // Función para refrescar manualmente
  const handleRefresh = () => {
    setLoading(true);
    fetchData();
  };

  return (
    <div>
      <button onClick={handleRefresh}>
        Refrescar
      </button>
      
      {/* Mostrar datos */}
      {loading ? (
        <p>Cargando...</p>
      ) : (
        <>
          <Stats data={stats} />
          <ExpensesTable expenses={expenses} />
        </>
      )}
    </div>
  );
}
```

#### Vue 3 Example

```javascript
<template>
  <div>
    <button @click="handleRefresh">Refrescar</button>
    
    <div v-if="loading">Cargando...</div>
    <div v-else>
      <Stats :data="stats" />
      <ExpensesTable :expenses="expenses" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import api from '@/services/api';

const expenses = ref([]);
const stats = ref(null);
const loading = ref(true);
let pollingInterval = null;

// Función para cargar datos
const fetchData = async () => {
  try {
    const [expensesRes, statsRes] = await Promise.all([
      api.get('/expenses'),
      api.get('/dashboard/stats')
    ]);
    
    expenses.value = expensesRes.data.data;
    stats.value = statsRes.data;
    loading.value = false;
  } catch (error) {
    console.error('Error fetching data:', error);
  }
};

// Función para refrescar manualmente
const handleRefresh = () => {
  loading.value = true;
  fetchData();
};

// Iniciar polling
onMounted(() => {
  fetchData();
  
  // Actualizar cada 30 segundos
  pollingInterval = setInterval(() => {
    fetchData();
  }, 30000);
});

// Detener polling al desmontar
onUnmounted(() => {
  if (pollingInterval) {
    clearInterval(pollingInterval);
  }
});
</script>
```

### Opción 2: Polling Inteligente (Más eficiente)

Solo hace polling cuando la pestaña está activa.

```javascript
import { useState, useEffect, useRef } from 'react';

function useSmartPolling(fetchFunction, interval = 30000) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const intervalRef = useRef(null);

  const fetchData = async () => {
    try {
      const result = await fetchFunction();
      setData(result);
      setLoading(false);
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  };

  const startPolling = () => {
    if (intervalRef.current) return;
    
    intervalRef.current = setInterval(() => {
      fetchData();
    }, interval);
  };

  const stopPolling = () => {
    if (intervalRef.current) {
      clearInterval(intervalRef.current);
      intervalRef.current = null;
    }
  };

  useEffect(() => {
    fetchData();
    startPolling();

    // Detener polling cuando la pestaña no está visible
    const handleVisibilityChange = () => {
      if (document.hidden) {
        stopPolling();
      } else {
        fetchData();
        startPolling();
      }
    };

    document.addEventListener('visibilitychange', handleVisibilityChange);

    return () => {
      stopPolling();
      document.removeEventListener('visibilitychange', handleVisibilityChange);
    };
  }, []);

  return { data, loading, refetch: fetchData };
}

// Uso
function Dashboard() {
  const { data: expenses, loading, refetch } = useSmartPolling(
    () => api.get('/expenses').then(res => res.data.data),
    30000
  );

  return (
    <div>
      <button onClick={refetch}>Refrescar</button>
      {loading ? <p>Cargando...</p> : <ExpensesTable expenses={expenses} />}
    </div>
  );
}
```

### Opción 3: Actualización Después de Crear

Actualiza la lista inmediatamente después de crear un gasto.

```javascript
// React
function ExpenseForm({ onExpenseCreated }) {
  const [formData, setFormData] = useState({});
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      const response = await api.post('/expenses', formData);
      
      // Notificar al componente padre que se creó un gasto
      if (onExpenseCreated) {
        onExpenseCreated(response.data.data);
      }

      // Limpiar formulario
      setFormData({});
      
      // Mostrar mensaje de éxito
      alert('Gasto creado exitosamente');
    } catch (error) {
      console.error('Error:', error);
      alert('Error al crear el gasto');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      {/* Campos del formulario */}
      <button type="submit" disabled={loading}>
        {loading ? 'Guardando...' : 'Registrar Gasto'}
      </button>
    </form>
  );
}

function Dashboard() {
  const [expenses, setExpenses] = useState([]);

  const handleExpenseCreated = (newExpense) => {
    // Agregar el nuevo gasto al inicio de la lista
    setExpenses(prev => [newExpense, ...prev]);
    
    // O recargar toda la lista
    fetchExpenses();
  };

  const fetchExpenses = async () => {
    const response = await api.get('/expenses');
    setExpenses(response.data.data);
  };

  return (
    <div>
      <ExpenseForm onExpenseCreated={handleExpenseCreated} />
      <ExpensesTable expenses={expenses} />
    </div>
  );
}
```

### Opción 4: WebSockets (Tiempo Real - Avanzado)

Para actualizaciones en tiempo real sin polling.

#### Backend - Laravel Broadcasting

1. Instalar Laravel Echo Server o usar Pusher:

```bash
composer require pusher/pusher-php-server
npm install --save-dev laravel-echo pusher-js
```

2. Configurar `.env`:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1
```

3. Crear evento:

```php
// app/Events/ExpenseCreated.php
<?php

namespace App\Events;

use App\Models\Expense;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpenseCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Expense $expense
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('expenses.' . $this->expense->user_id);
    }

    public function broadcastAs(): string
    {
        return 'expense.created';
    }
}
```

4. Disparar evento en el servicio:

```php
// app/Services/ExpenseService.php
use App\Events\ExpenseCreated;

public function create(array $data): Expense
{
    // ... código existente ...
    
    $expense = Expense::create($data);
    
    // Disparar evento
    broadcast(new ExpenseCreated($expense))->toOthers();
    
    return $expense;
}
```

#### Frontend - Laravel Echo

```javascript
// src/services/echo.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.REACT_APP_PUSHER_KEY,
  cluster: process.env.REACT_APP_PUSHER_CLUSTER,
  forceTLS: true,
  authEndpoint: 'http://localhost:8000/broadcasting/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  }
});

export default echo;
```

```javascript
// React Component
import { useEffect } from 'react';
import echo from './services/echo';

function Dashboard() {
  const [expenses, setExpenses] = useState([]);
  const userId = 1; // Obtener del usuario autenticado

  useEffect(() => {
    // Escuchar eventos de nuevos gastos
    echo.channel(`expenses.${userId}`)
      .listen('.expense.created', (e) => {
        console.log('Nuevo gasto creado:', e.expense);
        
        // Agregar a la lista
        setExpenses(prev => [e.expense, ...prev]);
        
        // Mostrar notificación
        showNotification('Nuevo gasto registrado');
      });

    return () => {
      echo.leaveChannel(`expenses.${userId}`);
    };
  }, [userId]);

  return (
    <div>
      <ExpensesTable expenses={expenses} />
    </div>
  );
}
```

## Recomendación por Caso de Uso

### Para tu caso actual (Dashboard simple):

**Opción 3: Actualización después de crear** + **Opción 1: Polling simple**

```javascript
// Componente principal
function Dashboard() {
  const [expenses, setExpenses] = useState([]);
  const [showForm, setShowForm] = useState(false);

  // Cargar gastos
  const fetchExpenses = async () => {
    const response = await api.get('/expenses');
    setExpenses(response.data.data);
  };

  // Cargar inicialmente
  useEffect(() => {
    fetchExpenses();
  }, []);

  // Polling cada 30 segundos
  useEffect(() => {
    const interval = setInterval(fetchExpenses, 30000);
    return () => clearInterval(interval);
  }, []);

  // Callback cuando se crea un gasto
  const handleExpenseCreated = () => {
    fetchExpenses(); // Recargar lista
    setShowForm(false); // Cerrar formulario
  };

  return (
    <div>
      <button onClick={() => setShowForm(true)}>
        Crear Nuevo Gasto
      </button>

      {showForm && (
        <ExpenseForm 
          onSuccess={handleExpenseCreated}
          onCancel={() => setShowForm(false)}
        />
      )}

      <ExpensesTable expenses={expenses} />
    </div>
  );
}
```

## Configuración Recomendada

```javascript
// config/polling.js
export const POLLING_CONFIG = {
  // Intervalo de polling en milisegundos
  INTERVAL: 30000, // 30 segundos
  
  // Intervalo cuando la pestaña no está activa
  BACKGROUND_INTERVAL: 60000, // 1 minuto
  
  // Endpoints que requieren polling
  ENDPOINTS: {
    expenses: '/api/v1/expenses',
    dashboard: '/api/v1/dashboard',
    stats: '/api/v1/dashboard/stats'
  }
};
```

## Mejores Prácticas

1. **No hagas polling muy frecuente** - 30 segundos es un buen balance
2. **Detén el polling cuando la pestaña no está visible** - Ahorra recursos
3. **Actualiza inmediatamente después de crear/editar** - Mejor UX
4. **Muestra indicadores de carga** - El usuario debe saber que algo está pasando
5. **Maneja errores de red** - El polling puede fallar
6. **Limpia los intervalos** - Evita memory leaks

## Indicador Visual de Actualización

```javascript
function Dashboard() {
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [lastUpdate, setLastUpdate] = useState(new Date());

  const fetchData = async () => {
    setIsRefreshing(true);
    try {
      // Cargar datos
      await fetchExpenses();
      setLastUpdate(new Date());
    } finally {
      setIsRefreshing(false);
    }
  };

  return (
    <div>
      <div className="update-indicator">
        {isRefreshing && <span>Actualizando...</span>}
        <span>Última actualización: {lastUpdate.toLocaleTimeString()}</span>
      </div>
      
      <ExpensesTable expenses={expenses} />
    </div>
  );
}
```

## Conclusión

Para tu caso, implementa:
1. **Actualización inmediata** después de crear un gasto
2. **Polling cada 30 segundos** para mantener los datos actualizados
3. **Botón de refrescar manual** para que el usuario pueda actualizar cuando quiera

Esto te dará una buena experiencia de usuario sin complicar demasiado el código.
