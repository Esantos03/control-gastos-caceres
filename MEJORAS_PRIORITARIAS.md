# 🚀 MEJORAS PRIORITARIAS - CONTROL DE GASTOS CÁCERES

**Fecha:** 18 de Febrero de 2026  
**Prioridad:** CRÍTICA  
**Tiempo Estimado:** 2-3 semanas

---

## ⚠️ PROBLEMAS CRÍTICOS QUE REQUIEREN ATENCIÓN INMEDIATA

### 🔴 1. MULTI-TENANCY (CRÍTICO - 12 horas)

**Problema:** No hay relación user_id en las tablas principales. Todos los usuarios pueden ver/editar gastos de otros usuarios.

**Impacto:** Violación de privacidad, datos compartidos entre usuarios.

**Solución:**

```bash
# Crear migración
php artisan make:migration add_user_id_to_main_tables
```

```php
// database/migrations/xxxx_add_user_id_to_main_tables.php
public function up(): void
{
    Schema::table('expenses', function (Blueprint $table) {
        $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
        $table->index('user_id');
    });
    
    Schema::table('categories', function (Blueprint $table) {
        $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
        $table->index('user_id');
    });
    
    Schema::table('subcategories', function (Blueprint $table) {
        $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
        $table->index('user_id');
    });
    
    Schema::table('cards', function (Blueprint $table) {
        $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
        $table->index('user_id');
    });
    
    Schema::table('merchants', function (Blueprint $table) {
        $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
        $table->index('user_id');
    });
}
```

```php
// Agregar a todos los modelos
protected static function booted()
{
    static::addGlobalScope('user', function ($query) {
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        }
    });
    
    static::creating(function ($model) {
        if (auth()->check() && !$model->user_id) {
            $model->user_id = auth()->id();
        }
    });
}
```

---

### 🔴 2. POLÍTICAS DE AUTORIZACIÓN (CRÍTICO - 8 horas)

**Problema:** No hay Policies implementadas. Cualquier usuario puede modificar datos de otros.

**Solución:**

```bash
# Crear policies
php artisan make:policy ExpensePolicy --model=Expense
php artisan make:policy CategoryPolicy --model=Category
php artisan make:policy CardPolicy --model=Card
```

```php
// app/Policies/ExpensePolicy.php
<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }
}
```

```php
// Registrar en AppServiceProvider.php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::policy(Expense::class, ExpensePolicy::class);
    Gate::policy(Category::class, CategoryPolicy::class);
    Gate::policy(Card::class, CardPolicy::class);
}
```

```php
// Usar en controladores
public function update(UpdateExpenseRequest $request, Expense $expense)
{
    $this->authorize('update', $expense);
    // ...
}
```

---

### 🔴 3. CONVERSIÓN AUTOMÁTICA DE MONEDA (CRÍTICO - 6 horas)

**Problema:** Usuario debe calcular manualmente exchange_rate y amount_converted.

**Solución:**

```php
// app/Filament/Resources/Expenses/Schemas/ExpenseForm.php

Select::make('currency_id')
    ->relationship('currency', 'name')
    ->required()
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        self::calculateConversion($set, $get);
    }),

TextInput::make('amount')
    ->numeric()
    ->required()
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        self::calculateConversion($set, $get);
    }),

DatePicker::make('expense_date')
    ->required()
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        self::calculateConversion($set, $get);
    }),

TextInput::make('exchange_rate')
    ->numeric()
    ->disabled()
    ->dehydrated()
    ->helperText('Se calcula automáticamente'),

TextInput::make('amount_converted')
    ->numeric()
    ->disabled()
    ->dehydrated()
    ->helperText('Se calcula automáticamente'),

// Método helper
private static function calculateConversion(callable $set, callable $get): void
{
    $amount = $get('amount');
    $currencyId = $get('currency_id');
    $date = $get('expense_date');
    
    if (!$amount || !$currencyId || !$date) {
        return;
    }
    
    $exchangeRateService = app(\App\Services\ExchangeRateService::class);
    $rate = $exchangeRateService->getRate($currencyId, \Carbon\Carbon::parse($date), 'average');
    
    if ($rate) {
        $set('exchange_rate', $rate);
        $set('amount_converted', round($amount * $rate, 2));
    }
}
```

```php
// app/Http/Controllers/Api/V1/ExpenseController.php

public function store(StoreExpenseRequest $request)
{
    $data = $request->validated();
    
    // Calcular automáticamente si no se proporciona
    if (empty($data['exchange_rate'])) {
        $exchangeRateService = app(\App\Services\ExchangeRateService::class);
        $data['exchange_rate'] = $exchangeRateService->getRate(
            $data['currency_id'],
            \Carbon\Carbon::parse($data['expense_date']),
            'average'
        ) ?? 1;
    }
    
    if (empty($data['amount_converted'])) {
        $data['amount_converted'] = round($data['amount'] * $data['exchange_rate'], 2);
    }
    
    $expense = Expense::create($data);
    
    return response()->json([
        'message' => 'Gasto creado exitosamente',
        'data' => new ExpenseResource($expense)
    ], 201);
}
```

---

### 🔴 4. VALIDACIONES ROBUSTAS (CRÍTICO - 8 horas)

**Problema:** Falta validación de relaciones y reglas de negocio.

**Solución:**

```php
// app/Rules/BelongsToCategory.php (ya existe, implementar uso)
<?php

namespace App\Rules;

use App\Models\Subcategory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BelongsToCategory implements ValidationRule
{
    public function __construct(private ?int $categoryId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->categoryId || !$value) {
            return;
        }

        $subcategory = Subcategory::find($value);
        
        if (!$subcategory || $subcategory->category_id !== $this->categoryId) {
            $fail('La subcategoría seleccionada no pertenece a la categoría.');
        }
    }
}
```

```php
// app/Rules/RequiresCardPaymentMethod.php (nuevo)
<?php

namespace App\Rules;

use App\Models\PaymentMethod;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RequiresCardPaymentMethod implements ValidationRule
{
    public function __construct(private ?int $paymentMethodId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return; // card_id es nullable
        }

        if (!$this->paymentMethodId) {
            $fail('Debe seleccionar un método de pago.');
            return;
        }

        $paymentMethod = PaymentMethod::find($this->paymentMethodId);
        
        if ($paymentMethod && !in_array($paymentMethod->name, ['Tarjeta de Crédito', 'Tarjeta de Débito'])) {
            $fail('Solo puede seleccionar una tarjeta si el método de pago es Tarjeta de Crédito o Débito.');
        }
    }
}
```

```php
// app/Http/Requests/Api/StoreExpenseRequest.php
public function rules(): array
{
    return [
        'expense_date' => ['required', 'date', 'before_or_equal:today'],
        'description' => ['required', 'string', 'max:255'],
        'amount' => ['required', 'numeric', 'min:0.01'],
        'currency_id' => ['required', 'exists:currencies,id'],
        'category_id' => ['required', 'exists:categories,id'],
        'subcategory_id' => [
            'required',
            'exists:subcategories,id',
            new BelongsToCategory($this->category_id)
        ],
        'payment_method_id' => ['required', 'exists:payment_methods,id'],
        'card_id' => [
            'nullable',
            'exists:cards,id',
            new RequiresCardPaymentMethod($this->payment_method_id)
        ],
        'merchant_id' => ['nullable', 'exists:merchants,id'],
        'installments' => ['integer', 'min:1', 'max:60'],
        'current_installment' => [
            'integer',
            'min:1',
            'lte:installments'
        ],
        'expense_type' => ['required', 'in:fixed,variable,occasional'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'is_paid' => ['boolean'],
    ];
}

public function messages(): array
{
    return [
        'current_installment.lte' => 'La cuota actual no puede ser mayor al número total de cuotas.',
        'expense_date.before_or_equal' => 'La fecha del gasto no puede ser futura.',
    ];
}
```

---

### 🔴 5. MANEJO ROBUSTO DE ERRORES (CRÍTICO - 6 horas)

**Problema:** Try-catch básico sin logs detallados, expone información sensible.

**Solución:**

```php
// app/Exceptions/ExpenseException.php (nuevo)
<?php

namespace App\Exceptions;

use Exception;

class ExpenseException extends Exception
{
    public static function creationFailed(string $message = ''): self
    {
        return new self("No se pudo crear el gasto. {$message}");
    }

    public static function updateFailed(string $message = ''): self
    {
        return new self("No se pudo actualizar el gasto. {$message}");
    }

    public static function deleteFailed(string $message = ''): self
    {
        return new self("No se pudo eliminar el gasto. {$message}");
    }
}
```

```php
// app/Services/ExpenseService.php
use App\Exceptions\ExpenseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

public function create(array $data): Expense
{
    try {
        return DB::transaction(function () use ($data) {
            // Auto-calcular tasa de cambio
            if (empty($data['exchange_rate'])) {
                $date = Carbon::parse($data['expense_date']);
                $data['exchange_rate'] = $this->exchangeRateService->getRate(
                    $data['currency_id'],
                    $date,
                    'average'
                ) ?? 1;
            }

            // Auto-calcular monto convertido
            if (empty($data['amount_converted'])) {
                $data['amount_converted'] = round($data['amount'] * $data['exchange_rate'], 2);
            }

            $expense = Expense::create($data);

            // Verificar presupuesto
            $this->budgetService->checkBudgetAlert($expense->category_id);

            Log::info('Expense created successfully', [
                'user_id' => auth()->id(),
                'expense_id' => $expense->id,
                'amount' => $expense->amount_converted
            ]);

            return $expense;
        });
    } catch (\Exception $e) {
        Log::error('Error creating expense', [
            'user_id' => auth()->id(),
            'data' => $data,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        throw ExpenseException::creationFailed($e->getMessage());
    }
}
```

```php
// app/Http/Controllers/Api/V1/ExpenseController.php
use App\Exceptions\ExpenseException;

public function store(StoreExpenseRequest $request): JsonResponse
{
    try {
        $expense = $this->expenseService->create($request->validated());

        return response()->json([
            'message' => 'Gasto creado exitosamente',
            'data' => new ExpenseResource($expense->load([
                'category', 'subcategory', 'card', 
                'currency', 'merchant', 'paymentMethod'
            ])),
        ], 201);
    } catch (ExpenseException $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('Unexpected error in ExpenseController@store', [
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'message' => 'Ocurrió un error inesperado. Por favor intente nuevamente.',
        ], 500);
    }
}
```

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

### Semana 1

- [ ] **Día 1-2:** Implementar Multi-Tenancy
  - [ ] Crear migración add_user_id_to_main_tables
  - [ ] Agregar user_id a: expenses, categories, subcategories, cards, merchants
  - [ ] Agregar Global Scopes a todos los modelos
  - [ ] Actualizar seeders para asignar user_id
  - [ ] Probar que cada usuario solo ve sus datos

- [ ] **Día 3:** Crear Policies
  - [ ] ExpensePolicy
  - [ ] CategoryPolicy
  - [ ] CardPolicy
  - [ ] Registrar en AppServiceProvider
  - [ ] Agregar authorize() en controladores

- [ ] **Día 4:** Conversión Automática
  - [ ] Actualizar ExpenseForm.php (Filament)
  - [ ] Actualizar ExpenseController.php (API)
  - [ ] Probar cálculos automáticos

- [ ] **Día 5:** Validaciones Robustas
  - [ ] Implementar uso de BelongsToCategory
  - [ ] Crear RequiresCardPaymentMethod
  - [ ] Actualizar FormRequests
  - [ ] Probar validaciones

### Semana 2

- [ ] **Día 1-2:** Manejo de Errores
  - [ ] Crear ExpenseException
  - [ ] Actualizar ExpenseService con try-catch
  - [ ] Actualizar todos los controladores
  - [ ] Agregar logs detallados

- [ ] **Día 3-4:** Testing
  - [ ] Tests para multi-tenancy
  - [ ] Tests para policies
  - [ ] Tests para conversión automática
  - [ ] Tests para validaciones

- [ ] **Día 5:** Documentación y Limpieza
  - [ ] Actualizar README.md
  - [ ] Documentar cambios en CHANGELOG.md
  - [ ] Code review
  - [ ] Merge a main

---

## 🧪 COMANDOS DE TESTING

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed --class=CompleteSeeder

# Ejecutar tests
php artisan test

# Limpiar cache
php artisan optimize:clear

# Verificar rutas
php artisan route:list

# Verificar policies
php artisan tinker
>>> Gate::allows('update', $expense)
```

---

## 📊 MÉTRICAS DE ÉXITO

Después de implementar estas mejoras:

- ✅ Cada usuario solo ve sus propios datos
- ✅ No se pueden modificar datos de otros usuarios
- ✅ Conversión de moneda es automática
- ✅ Validaciones previenen datos inconsistentes
- ✅ Errores se registran correctamente
- ✅ Tests pasan al 100%
- ✅ Cobertura de tests > 60%

---

## ⚠️ ADVERTENCIAS

1. **Backup de Base de Datos:** Hacer backup antes de ejecutar migraciones
2. **Datos Existentes:** La migración de user_id requerirá asignar un usuario a datos existentes
3. **Testing:** Probar exhaustivamente en desarrollo antes de producción
4. **Cache:** Limpiar cache después de cambios importantes

---

## 📞 SOPORTE

Si encuentras problemas durante la implementación:

1. Revisar logs en `storage/logs/laravel.log`
2. Verificar configuración en `.env`
3. Ejecutar `php artisan optimize:clear`
4. Consultar documentación de Laravel/Filament

---

**Preparado por:** Kiro AI  
**Fecha:** 18 de Febrero de 2026  
**Prioridad:** CRÍTICA ⚠️

