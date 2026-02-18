<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Información Básica
                DatePicker::make('expense_date')
                    ->label('Fecha del Gasto')
                    ->required()
                    ->default(now())
                    ->native(false),
                
                Select::make('expense_type')
                    ->label('Tipo de Gasto')
                    ->options([
                        'fixed' => 'Fijo',
                        'variable' => 'Variable',
                        'occasional' => 'Ocasional',
                    ])
                    ->required()
                    ->default('variable'),
                
                TextInput::make('description')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                
                // Monto y Moneda
                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->step('0.01')
                    ->minValue(0)
                    ->prefix('$'),
                
                Select::make('currency_id')
                    ->relationship('currency', 'name')
                    ->label('Moneda')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                TextInput::make('exchange_rate')
                    ->label('Tasa de Cambio')
                    ->numeric()
                    ->step('0.0001')
                    ->minValue(0)
                    ->suffix('DOP')
                    ->helperText('Dejar vacío para usar la tasa del mes'),
                
                TextInput::make('amount_converted')
                    ->label('Monto Convertido (DOP)')
                    ->required()
                    ->numeric()
                    ->step('0.01')
                    ->minValue(0)
                    ->prefix('RD$'),
                
                // Categorización
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Categoría')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                Select::make('subcategory_id')
                    ->relationship('subcategory', 'name')
                    ->label('Subcategoría')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                // Método de Pago
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->label('Método de Pago')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if (!$state) return;
                        
                        $paymentMethod = \App\Models\PaymentMethod::find($state);
                        if (!$paymentMethod) return;
                        
                        // Si no es TC (Tarjeta de Crédito), limpiar card_id
                        if ($paymentMethod->name !== 'TC') {
                            $set('card_id', null);
                        }
                        
                        // Si no es cheque, limpiar check_number
                        if ($paymentMethod->name !== 'CHEQUE') {
                            $set('check_number', null);
                        }
                    }),
                
                Select::make('card_id')
                    ->relationship('card', 'name')
                    ->label('Tarjeta')
                    ->searchable()
                    ->preload()
                    ->visible(function ($get) {
                        $paymentMethodId = $get('payment_method_id');
                        if (!$paymentMethodId) return false;
                        
                        $paymentMethod = \App\Models\PaymentMethod::find($paymentMethodId);
                        if (!$paymentMethod) return false;
                        
                        return $paymentMethod->name === 'TC';
                    }),
                
                TextInput::make('check_number')
                    ->label('No. de Cheque')
                    ->maxLength(50)
                    ->visible(function ($get) {
                        $paymentMethodId = $get('payment_method_id');
                        if (!$paymentMethodId) return false;
                        
                        $paymentMethod = \App\Models\PaymentMethod::find($paymentMethodId);
                        if (!$paymentMethod) return false;
                        
                        return $paymentMethod->name === 'CHEQUE';
                    }),
                
                Select::make('merchant_id')
                    ->relationship('merchant', 'name')
                    ->label('Comercio')
                    ->searchable()
                    ->preload(),
                
                // Cuotas y Pagos Recurrentes
                TextInput::make('installments')
                    ->label('Número de Cuotas')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(60)
                    ->helperText('Total de cuotas del gasto'),
                
                TextInput::make('current_installment')
                    ->label('Cuota Actual')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->helperText('Número de la cuota actual'),
                
                Select::make('parent_expense_id')
                    ->relationship('parentExpense', 'description')
                    ->label('Gasto Padre')
                    ->searchable()
                    ->preload()
                    ->helperText('Si este gasto es una cuota de otro gasto'),
                
                // Información Adicional
                Toggle::make('is_paid')
                    ->label('¿Está Pagado?')
                    ->default(true)
                    ->inline(false),
                
                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }
}
