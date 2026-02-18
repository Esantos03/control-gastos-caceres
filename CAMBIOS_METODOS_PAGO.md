# Cambios en Métodos de Pago - Gastos

## Resumen
Se implementó funcionalidad para mostrar/ocultar campos según el método de pago seleccionado en el formulario de gastos.

## Cambios Realizados

### 1. Base de Datos
- **Nueva migración**: `2026_02_18_163459_add_check_number_to_expenses_table.php`
- **Campo agregado**: `check_number` (string, nullable) en la tabla `expenses`

### 2. Modelo Expense
- Agregado `check_number` al array `$fillable`

### 3. Formulario de Filament (ExpenseForm.php)
- **Campo payment_method_id**: 
  - Agregado `->live()` para reactividad
  - Agregado `afterStateUpdated()` para limpiar campos cuando cambia el método de pago
  
- **Campo card_id**:
  - Agregado `->visible()` con lógica condicional
  - Solo se muestra cuando el método de pago es "Tarjeta de Crédito" o "Tarjeta de Débito"
  
- **Campo check_number** (NUEVO):
  - Campo de texto para número de cheque
  - Solo se muestra cuando el método de pago es "Cheque"
  - Máximo 50 caracteres

### 4. Tabla de Filament (ExpensesTable.php)
- Agregada columna `check_number` con placeholder "N/A"
- Columna toggleable (se puede ocultar/mostrar)

### 5. API
- **StoreExpenseRequest**: Agregada validación para `check_number` (nullable, string, max:50)
- **UpdateExpenseRequest**: Agregada validación para `check_number` (nullable, string, max:50)
- **ExpenseResource**: Agregado campo `check_number` en la respuesta JSON

### 6. Seeders
- **CompleteSeeder**: Agregado "Cheque" a la lista de métodos de pago

## Comportamiento

### Cuando se selecciona "Efectivo"
- ❌ Campo "Tarjeta" oculto
- ❌ Campo "No. de Cheque" oculto

### Cuando se selecciona "Cheque"
- ❌ Campo "Tarjeta" oculto
- ✅ Campo "No. de Cheque" visible

### Cuando se selecciona "Tarjeta de Crédito" o "Tarjeta de Débito"
- ✅ Campo "Tarjeta" visible
- ❌ Campo "No. de Cheque" oculto

### Cuando se selecciona "Transferencia"
- ❌ Campo "Tarjeta" oculto
- ❌ Campo "No. de Cheque" oculto

## Métodos de Pago Disponibles
1. Efectivo
2. Tarjeta de Crédito
3. Tarjeta de Débito
4. Transferencia
5. Cheque (nuevo)

## Notas Técnicas
- Los campos se ocultan/muestran dinámicamente usando `->visible()` con callbacks
- Se utiliza `->live()` en el select de método de pago para reactividad en tiempo real
- Los valores se limpian automáticamente cuando se cambia el método de pago usando `afterStateUpdated()`
