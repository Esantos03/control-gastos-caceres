<?php

namespace App\Filament\Resources\Cards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la Tarjeta')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: AMEX, BHD León, Visa'),
                
                TextInput::make('last_digits')
                    ->label('Últimos 4 Dígitos')
                    ->required()
                    ->maxLength(4)
                    ->minLength(4)
                    ->numeric()
                    ->placeholder('1234'),
                
                Select::make('type')
                    ->label('Tipo de Tarjeta')
                    ->options([
                        'credito' => 'Crédito',
                        'debito' => 'Débito'
                    ])
                    ->required()
                    ->default('credito'),
                
                DatePicker::make('expiration_date')
                    ->label('Fecha de Vencimiento')
                    ->native(false)
                    ->displayFormat('m/Y')
                    ->helperText('Fecha de vencimiento de la tarjeta'),
                
                TextInput::make('credit_limit')
                    ->label('Límite de Crédito')
                    ->numeric()
                    ->step('0.01')
                    ->minValue(0)
                    ->prefix('RD$')
                    ->helperText('Solo para tarjetas de crédito'),
                
                TextInput::make('billing_day')
                    ->label('Día de Corte')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31)
                    ->helperText('Día del mes en que se genera el estado de cuenta'),
                
                TextInput::make('payment_day')
                    ->label('Día de Pago')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31)
                    ->helperText('Día límite de pago del mes'),
                
                Toggle::make('is_active')
                    ->label('¿Tarjeta Activa?')
                    ->default(true)
                    ->inline(false)
                    ->helperText('Si la tarjeta está activa y disponible para usar'),
                
                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->maxLength(1000)
                    ->helperText('Información adicional sobre la tarjeta')
                    ->columnSpanFull(),
            ]);
    }
}
