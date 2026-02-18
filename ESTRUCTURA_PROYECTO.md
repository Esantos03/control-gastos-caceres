# 🏗️ ESTRUCTURA DEL PROYECTO - CONTROL DE GASTOS CÁCERES

**Fecha:** 18 de Febrero de 2026  
**Versión:** 1.0

---

## 📁 ESTRUCTURA DE DIRECTORIOS

```
control-gastos-caceres/
│
├── 📄 Documentación Principal
│   ├── README.md                              # Guía de inicio rápido
│   ├── RESUMEN_EJECUTIVO.md                   # Resumen para decisores
│   ├── MEJORAS_PRIORITARIAS.md                # ⚠️ Mejoras críticas
│   ├── ANALISIS_Y_RECOMENDACIONES_FINAL.md    # Análisis completo
│   ├── CHANGELOG.md                           # Historial de cambios
│   ├── ESTRUCTURA_PROYECTO.md                 # Este archivo
│   │
│   ├── 📚 Documentación Técnica
│   ├── API_DOCUMENTATION.md                   # Documentación API REST
│   ├── FILAMENT_SETUP.md                      # Configuración Filament
│   ├── ANALISIS_EXCEL.md                      # Análisis Excel original
│   ├── ANALISIS_COMPLETO_PROYECTO.md          # Análisis detallado
│   └── MEJORAS_IMPLEMENTADAS.md               # Historial de mejoras
│
├── 🎨 Frontend & Assets
│   ├── resources/
│   │   ├── css/                               # Estilos CSS
│   │   ├── js/                                # JavaScript
│   │   └── views/                             # Vistas Blade
│   │       └── filament/                      # Vistas personalizadas Filament
│   │
│   └── public/                                # Assets públicos
│       ├── css/filament/                      # CSS compilado Filament
│       ├── js/filament/                       # JS compilado Filament
│       ├── fonts/                             # Fuentes
│       └── logo.png                           # Logo
│
├── 🔧 Backend (Laravel)
│   ├── app/
│   │   │
│   │   ├── 🎛️ Filament (Panel Admin)
│   │   │   ├── Pages/
│   │   │   │   └── Dashboard.php              # Dashboard principal
│   │   │   │
│   │   │   ├── Resources/                     # Recursos CRUD
│   │   │   │   ├── Cards/                     # Tarjetas
│   │   │   │   │   ├── CardResource.php
│   │   │   │   │   ├── Pages/                 # Páginas CRUD
│   │   │   │   │   ├── Schemas/               # Formularios
│   │   │   │   │   └── Tables/                # Tablas
│   │   │   │   │
│   │   │   │   ├── Categories/                # Categorías
│   │   │   │   ├── Currencies/                # Monedas
│   │   │   │   ├── ExchangeRates/             # Tasas de cambio
│   │   │   │   ├── Expenses/                  # Gastos
│   │   │   │   ├── Merchants/                 # Comercios
│   │   │   │   ├── PaymentMethods/            # Métodos de pago
│   │   │   │   └── Subcategories/             # Subcategorías
│   │   │   │
│   │   │   └── Widgets/                       # Widgets del dashboard
│   │   │       ├── ExpensesStatsWidget.php
│   │   │       └── ExpensesTableWidget.php
│   │   │
│   │   ├── 🌐 HTTP (API & Web)
│   │   │   ├── Controllers/
│   │   │   │   ├── Controller.php             # Base controller
│   │   │   │   └── Api/V1/                    # API REST v1
│   │   │   │       ├── AuthController.php     # Autenticación
│   │   │   │       ├── CardController.php
│   │   │   │       ├── CategoryController.php
│   │   │   │       ├── CurrencyController.php
│   │   │   │       ├── DashboardController.php
│   │   │   │       ├── ExchangeRateController.php
│   │   │   │       ├── ExpenseController.php
│   │   │   │       ├── MerchantController.php
│   │   │   │       ├── PaymentMethodController.php
│   │   │   │       ├── ReportController.php
│   │   │   │       └── SubcategoryController.php
│   │   │   │
│   │   │   ├── Middleware/
│   │   │   │   └── HandleCors.php             # CORS middleware
│   │   │   │
│   │   │   ├── Requests/Api/                  # Form Requests
│   │   │   │   ├── LoginRequest.php
│   │   │   │   ├── RegisterRequest.php
│   │   │   │   ├── StoreExpenseRequest.php
│   │   │   │   ├── UpdateExpenseRequest.php
│   │   │   │   └── ... (otros requests)
│   │   │   │
│   │   │   └── Resources/                     # API Resources
│   │   │       ├── CardResource.php
│   │   │       ├── CategoryResource.php
│   │   │       ├── ExpenseResource.php
│   │   │       └── ... (otros resources)
│   │   │
│   │   ├── 📦 Models (Eloquent)
│   │   │   ├── User.php                       # Usuario
│   │   │   ├── Expense.php                    # Gasto
│   │   │   ├── Category.php                   # Categoría
│   │   │   ├── Subcategory.php                # Subcategoría
│   │   │   ├── Card.php                       # Tarjeta
│   │   │   ├── PaymentMethod.php              # Método de pago
│   │   │   ├── Currency.php                   # Moneda
│   │   │   ├── ExchangeRate.php               # Tasa de cambio
│   │   │   └── Merchant.php                   # Comercio
│   │   │
│   │   ├── 🔧 Services (Lógica de Negocio)
│   │   │   ├── ExpenseService.php             # Gestión de gastos
│   │   │   ├── BudgetService.php              # Gestión de presupuestos
│   │   │   └── ExchangeRateService.php        # Gestión de tasas
│   │   │
│   │   ├── 📏 Rules (Validaciones Custom)
│   │   │   ├── BelongsToCategory.php          # Validar subcategoría
│   │   │   ├── ValidBillingCycle.php          # Validar ciclo facturación
│   │   │   └── ValidInstallmentNumber.php     # Validar número de cuota
│   │   │
│   │   ├── 🎮 Console (Comandos)
│   │   │   └── Commands/
│   │   │       ├── CheckUsers.php
│   │   │       └── ResetUserPassword.php
│   │   │
│   │   └── 🔌 Providers (Service Providers)
│   │       ├── AppServiceProvider.php
│   │       └── Filament/
│   │           └── AdminPanelProvider.php
│   │
│   ├── 🗄️ Database
│   │   ├── migrations/                        # Migraciones
│   │   │   ├── 2026_01_29_152250_create_categories_table.php
│   │   │   ├── 2026_01_29_152443_create_subcategories_table.php
│   │   │   ├── 2026_01_29_152535_create_currencies_table.php
│   │   │   ├── 2026_01_29_152639_create_exchange_rates_table.php
│   │   │   ├── 2026_01_29_152713_create_payment_methods_table.php
│   │   │   ├── 2026_01_29_152747_create_cards_table.php
│   │   │   ├── 2026_01_29_152843_create_merchants_table.php
│   │   │   ├── 2026_01_29_153019_create_expenses_table.php
│   │   │   ├── 2026_02_06_184210_add_additional_fields_to_expenses_table.php
│   │   │   ├── 2026_02_06_184239_add_additional_fields_to_categories_table.php
│   │   │   ├── 2026_02_06_184405_add_additional_fields_to_cards_table.php
│   │   │   └── 2026_02_18_163459_add_check_number_to_expenses_table.php
│   │   │
│   │   ├── seeders/                           # Seeders
│   │   │   ├── DatabaseSeeder.php
│   │   │   └── CompleteSeeder.php             # Seeder completo con datos
│   │   │
│   │   ├── factories/                         # Factories
│   │   │   └── UserFactory.php
│   │   │
│   │   └── database.sqlite                    # Base de datos SQLite
│   │
│   ├── 🧪 Tests
│   │   ├── Unit/                              # Tests unitarios
│   │   │   ├── BudgetServiceTest.php
│   │   │   └── ExpenseServiceTest.php
│   │   │
│   │   ├── Feature/                           # Tests de integración
│   │   │   └── (vacío - pendiente)
│   │   │
│   │   └── TestCase.php                       # Base test case
│   │
│   ├── 🛣️ Routes
│   │   ├── web.php                            # Rutas web
│   │   ├── api.php                            # Rutas API
│   │   └── console.php                        # Comandos console
│   │
│   ├── ⚙️ Config
│   │   ├── app.php                            # Configuración app
│   │   ├── auth.php                           # Autenticación
│   │   ├── database.php                       # Base de datos
│   │   ├── cors.php                           # CORS
│   │   ├── sanctum.php                        # Sanctum (API tokens)
│   │   └── ... (otros configs)
│   │
│   └── 📦 Storage
│       ├── app/                               # Archivos de aplicación
│       ├── framework/                         # Cache y sesiones
│       │   ├── cache/
│       │   ├── sessions/
│       │   └── views/                         # Vistas compiladas
│       └── logs/                              # Logs
│           └── laravel.log
│
├── 🔒 Configuración
│   ├── .env.example                           # Ejemplo de variables
│   ├── .env                                   # Variables de entorno (no en repo)
│   ├── .gitignore                             # Archivos ignorados
│   ├── .editorconfig                          # Configuración editor
│   ├── composer.json                          # Dependencias PHP
│   ├── package.json                           # Dependencias Node
│   ├── vite.config.js                         # Configuración Vite
│   └── phpunit.xml                            # Configuración PHPUnit
│
└── 🚀 Bootstrap
    ├── app.php                                # Bootstrap aplicación
    └── cache/                                 # Cache de bootstrap
```

---

## 📊 ESTADÍSTICAS DEL PROYECTO

### Archivos por Tipo
```
PHP:        ~150 archivos
Blade:      ~20 archivos
JavaScript: ~30 archivos
CSS:        ~10 archivos
Markdown:   11 archivos
Config:     ~15 archivos
```

### Líneas de Código (Estimado)
```
Backend (PHP):     ~8,000 líneas
Frontend (Blade):  ~1,500 líneas
Tests:             ~500 líneas
Config:            ~1,000 líneas
Total:             ~11,000 líneas
```

### Modelos y Relaciones
```
Modelos:           9
Migraciones:       13
Seeders:           2
Factories:         1
Policies:          0 ⚠️ (pendiente)
```

### API Endpoints
```
Públicos:          2 (login, register)
Protegidos:        ~60 endpoints
Versión:           v1
Autenticación:     Laravel Sanctum
```

---

## 🎯 COMPONENTES PRINCIPALES

### 1. Modelos (9)
```
User          → Usuario del sistema
Expense       → Gasto registrado
Category      → Categoría de gasto
Subcategory   → Subcategoría de gasto
Card          → Tarjeta de crédito/débito
PaymentMethod → Método de pago
Currency      → Moneda
ExchangeRate  → Tasa de cambio
Merchant      → Comercio/establecimiento
```

### 2. Servicios (3)
```
ExpenseService      → Gestión de gastos y cuotas
BudgetService       → Control de presupuestos
ExchangeRateService → Conversión de moneda
```

### 3. Controladores API (11)
```
AuthController          → Autenticación
ExpenseController       → Gastos
CategoryController      → Categorías
SubcategoryController   → Subcategorías
CardController          → Tarjetas
PaymentMethodController → Métodos de pago
CurrencyController      → Monedas
ExchangeRateController  → Tasas de cambio
MerchantController      → Comercios
DashboardController     → Dashboard
ReportController        → Reportes
```

### 4. Recursos Filament (8)
```
ExpenseResource       → CRUD de gastos
CategoryResource      → CRUD de categorías
SubcategoryResource   → CRUD de subcategorías
CardResource          → CRUD de tarjetas
PaymentMethodResource → CRUD de métodos de pago
CurrencyResource      → CRUD de monedas
ExchangeRateResource  → CRUD de tasas
MerchantResource      → CRUD de comercios
```

---

## 🔗 RELACIONES ENTRE MODELOS

```
User (pendiente implementar)
  └─ hasMany → Expense
  └─ hasMany → Category
  └─ hasMany → Card
  └─ hasMany → Merchant

Expense
  ├─ belongsTo → User (pendiente)
  ├─ belongsTo → Category
  ├─ belongsTo → Subcategory
  ├─ belongsTo → Card
  ├─ belongsTo → PaymentMethod
  ├─ belongsTo → Currency
  ├─ belongsTo → Merchant
  ├─ belongsTo → Expense (parent)
  └─ hasMany → Expense (children/cuotas)

Category
  ├─ belongsTo → User (pendiente)
  ├─ hasMany → Subcategory
  └─ hasMany → Expense

Card
  ├─ belongsTo → User (pendiente)
  └─ hasMany → Expense

Currency
  ├─ hasMany → ExchangeRate
  └─ hasMany → Expense

ExchangeRate
  └─ belongsTo → Currency
```

---

## 📦 DEPENDENCIAS PRINCIPALES

### Backend (PHP)
```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "laravel/sanctum": "^4.3",
  "filament/filament": "^5.1",
  "laravel/tinker": "^2.10.1"
}
```

### Frontend (Node)
```json
{
  "tailwindcss": "^4.0.0",
  "vite": "^7.0.7",
  "axios": "^1.11.0"
}
```

---

## 🔧 COMANDOS ÚTILES

### Desarrollo
```bash
# Iniciar servidor
php artisan serve

# Compilar assets
npm run dev

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Limpiar cache
php artisan optimize:clear
```

### Testing
```bash
# Ejecutar tests
php artisan test

# Con cobertura
php artisan test --coverage

# Tests específicos
php artisan test --filter ExpenseTest
```

### Filament
```bash
# Crear usuario admin
php artisan make:filament-user

# Crear recurso
php artisan make:filament-resource NombreModelo

# Actualizar Filament
php artisan filament:upgrade
```

---

## 📈 MÉTRICAS DE COMPLEJIDAD

### Complejidad por Módulo
```
Expenses:       ⭐⭐⭐⭐⭐ (Alta - cuotas, conversión)
Categories:     ⭐⭐⭐ (Media - presupuestos)
Cards:          ⭐⭐⭐ (Media - límites, ciclos)
ExchangeRates:  ⭐⭐⭐ (Media - conversión)
Dashboard:      ⭐⭐⭐⭐ (Alta - estadísticas)
Reports:        ⭐⭐⭐⭐ (Alta - análisis)
API:            ⭐⭐⭐ (Media - CRUD estándar)
```

### Prioridad de Mantenimiento
```
1. Expenses (crítico - core del sistema)
2. Categories (importante - organización)
3. ExchangeRates (importante - conversión)
4. Cards (importante - pagos)
5. Dashboard (importante - visualización)
6. Reports (deseable - análisis)
7. Otros (bajo - soporte)
```

---

## 🎨 TECNOLOGÍAS UTILIZADAS

### Backend
- **Framework:** Laravel 12
- **Admin Panel:** Filament 5
- **API Auth:** Laravel Sanctum
- **Database:** SQLite (dev), MySQL/PostgreSQL (prod)
- **ORM:** Eloquent

### Frontend
- **CSS Framework:** Tailwind CSS 4
- **Build Tool:** Vite 7
- **Icons:** Heroicons
- **Charts:** (pendiente - Chart.js o ApexCharts)

### Testing
- **Framework:** PHPUnit
- **Coverage:** (pendiente configurar)

### DevOps
- **Version Control:** Git
- **Package Manager:** Composer (PHP), NPM (Node)
- **Environment:** Laravel Sail (opcional)

---

## 📝 NOTAS IMPORTANTES

### ⚠️ Pendientes Críticos
1. Implementar multi-tenancy (user_id)
2. Crear policies de autorización
3. Automatizar conversión de moneda
4. Agregar validaciones robustas
5. Mejorar manejo de errores

### ✅ Completados
1. Estructura base del proyecto
2. Modelos y relaciones
3. CRUD completo en Filament
4. API REST básica
5. Servicios de negocio
6. Tests unitarios básicos
7. Documentación completa

### 🔜 Próximos Pasos
1. Implementar mejoras críticas (Fase 0)
2. Agregar notificaciones (Fase 2)
3. Mejorar dashboard (Fase 2)
4. Completar tests (Fase 3)
5. Deploy a producción

---

**Última actualización:** 18 de Febrero de 2026  
**Mantenido por:** Equipo de Desarrollo

