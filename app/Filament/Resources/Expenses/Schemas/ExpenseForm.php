<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('expense_date')
                    ->label('Fecha del Gasto')
                    ->required()
                    ->default(now())
                    ->native(false),
                TextInput::make('description')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
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
                    ->step('0.01')
                    ->minValue(0)
                    ->suffix('DOP'),
                TextInput::make('amount_converted')
                    ->label('Monto Convertido')
                    ->required()
                    ->numeric()
                    ->step('0.01')
                    ->minValue(0)
                    ->prefix('$')
                    ->suffix('DOP'),
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
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->label('Método de Pago')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('card_id')
                    ->relationship('card', 'name')
                    ->label('Tarjeta')
                    ->searchable()
                    ->preload(),
                Select::make('merchant_id')
                    ->relationship('merchant', 'name')
                    ->label('Comercio')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
