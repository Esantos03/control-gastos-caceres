# 💰 Control de Gastos Cáceres

Sistema avanzado de control de gastos personales desarrollado con Laravel 12 y Filament 5. Diseñado para replicar y mejorar la funcionalidad de hojas de cálculo Excel con una interfaz moderna y funcionalidades automatizadas.

## ✨ Características Principales

### 📊 Gestión de Gastos
- ✅ Registro completo de gastos con múltiples campos
- ✅ Tipos de gastos: Fijos, Variables y Ocasionales
- ✅ Sistema de cuotas/pagos recurrentes
- ✅ Conversión automática de moneda
- ✅ Notas y estado de pago

### 🏷️ Categorización Inteligente
- ✅ Categorías con colores e iconos personalizados
- ✅ Subcategorías organizadas
- ✅ Presupuestos mensuales por categoría
- ✅ Alertas de presupuesto excedido

### 💳 Gestión de Tarjetas
- ✅ Tarjetas de crédito y débito
- ✅ Límites de crédito
- ✅ Días de corte y pago
- ✅ Seguimiento de uso de crédito

### 💱 Multi-Moneda
- ✅ Soporte para múltiples monedas
- ✅ Tasas de cambio mensuales
- ✅ Conversión automática a moneda base (DOP)
- ✅ Historial de tasas

### 📈 Dashboard y Reportes
- ✅ Widgets informativos
- ✅ Estadísticas en tiempo real
- ✅ Resumen de gastos por tarjeta
- ✅ Seguimiento de tasas de cambio

## 🚀 Instalación

### Requisitos Previos
- PHP 8.2 o superior
- Composer
- Node.js y NPM
- SQLite/MySQL/PostgreSQL

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd control-gastos-caceres
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node**
```bash
npm install
```

4. **Configurar el entorno**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurar la base de datos**
Editar `.env` y configurar la conexión a la base de datos:
```env
DB_CONNECTION=sqlite
# O para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=control_gastos
# DB_USERNAME=root
# DB_PASSWORD=
```

6. **Ejecutar migraciones**
```bash
php artisan migrate
```

7. **Crear usuario administrador**
```bash
php artisan make:filament-user
```

8. **Compilar assets**
```bash
npm run build
```

9. **Iniciar el servidor**
```bash
php artisan serve
```

10. **Acceder al panel**
Abrir navegador en: `http://localhost:8000/admin`

## 📚 Documentación

- **[README.md](README.md)** - Guía de inicio rápido
- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Documentación completa de la API REST
- **[CHANGELOG.md](CHANGELOG.md)** - Historial de cambios

## 🏗️ Arquitectura

### Estructura del Proyecto
```
app/
├── Filament/           # Recursos de Filament
│   ├── Pages/         # Páginas personalizadas
│   ├── Resources/     # Recursos CRUD
│   └── Widgets/       # Widgets del dashboard
├── Models/            # Modelos Eloquent
├── Services/          # Lógica de negocio
│   ├── BudgetService.php
│   ├── ExpenseService.php
│   └── ExchangeRateService.php
└── Rules/             # Validaciones personalizadas
```

### Modelos Principales
- **Expense** - Gastos
- **Category** - Categorías
- **Subcategory** - Subcategorías
- **Card** - Tarjetas
- **PaymentMethod** - Métodos de pago
- **Currency** - Monedas
- **ExchangeRate** - Tasas de cambio
- **Merchant** - Comercios

## 🔧 Servicios

### ExpenseService
Gestión de gastos con conversión automática de moneda y generación de cuotas.

```php
$expenseService->create([
    'amount' => 1000,
    'currency_id' => 1,
    'expense_date' => now(),
    // exchange_rate y amount_converted se calculan automáticamente
]);
```

### BudgetService
Control de presupuestos con alertas automáticas.

```php
$budgetService->isBudgetExceeded($categoryId);
$budgetService->getBudgetUsagePercentage($categoryId);
```

### ExchangeRateService
Gestión de tasas de cambio con cache.

```php
$rate = $exchangeRateService->getRate($currencyId, $date, 'average');
$converted = $exchangeRateService->convert($amount, $currencyId, $date);
```

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter ExpenseTest

# Con cobertura
php artisan test --coverage
```

## 🛠️ Comandos Útiles

```bash
# Limpiar cache
php artisan optimize:clear

# Regenerar cache
php artisan optimize

# Ver rutas
php artisan route:list

# Crear nuevo recurso de Filament
php artisan make:filament-resource NombreModelo --generate

# Refrescar base de datos (¡CUIDADO! Elimina datos)
php artisan migrate:fresh --seed
```

## 📊 Características Avanzadas

### Conversión Automática de Moneda
El sistema busca automáticamente la tasa de cambio del mes correspondiente y convierte los montos a la moneda base (DOP).

### Sistema de Cuotas
Genera automáticamente cuotas mensuales para gastos a plazos, manteniendo la relación con el gasto padre.

### Alertas de Presupuesto
Notifica cuando se alcanza el 80%, 90% o 100% del presupuesto mensual de una categoría.

### Cache Inteligente
Las tasas de cambio y estadísticas se cachean para mejorar el rendimiento.

## 🔐 Seguridad

### Implementado
- ✅ CSRF Protection habilitado
- ✅ XSS Protection mediante Blade
- ✅ SQL Injection Protection con Eloquent
- ✅ Autenticación con Laravel Sanctum
- ✅ Validaciones básicas en formularios

## 🚧 Roadmap

### Fase 1 - Completada ✅
- [x] Estructura base con Laravel y Filament
- [x] Modelos y migraciones
- [x] CRUD completo
- [x] Sistema de cuotas
- [x] Capa de servicios
- [x] API REST básica
- [x] Tests unitarios básicos

### Fase 2 - Planificado
- [ ] Sistema de notificaciones
- [ ] Dashboard mejorado con gráficos
- [ ] Reportes avanzados
- [ ] Exportación a Excel/PDF
- [ ] Cache estratégico
- [ ] Optimización de queries

### Fase 3 - Futuro
- [ ] Tests completos (80%+ cobertura)
- [ ] Eventos y Listeners
- [ ] Importación desde Excel
- [ ] PWA (Progressive Web App)
- [ ] Integración con APIs bancarias

## 🤝 Contribución

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Licencia

Este proyecto es privado y de uso personal.

## 👨‍💻 Autor

**Control de Gastos Cáceres**  
Desarrollado con ❤️ usando Laravel y Filament

## 📞 Soporte

Para preguntas o problemas:
1. Revisar la documentación en la carpeta raíz
2. Consultar los logs en `storage/logs/laravel.log`
3. Verificar la configuración en `.env`

## 🙏 Agradecimientos

- [Laravel](https://laravel.com) - Framework PHP
- [Filament](https://filamentphp.com) - Panel de administración
- [Tailwind CSS](https://tailwindcss.com) - Framework CSS

---

**Versión:** 1.0  
**Última actualización:** Febrero 2026
