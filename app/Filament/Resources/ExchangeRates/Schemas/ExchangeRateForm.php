<?php

namespace App\Filament\Resources\ExchangeRates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExchangeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('currency_id')
                    ->relationship('currency', 'name')
                    ->label('Moneda')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('month')
                    ->label('Mes')
                    ->required()
                    ->options([
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre',
                    ])
                    ->default(now()->month)
                    ->native(false),
                TextInput::make('year')
                    ->label('Año')
                    ->required()
                    ->numeric()
                    ->minValue(2020)
                    ->maxValue(2050)
                    ->default(now()->year),
                TextInput::make('buy_rate')
                    ->label('Tasa de Compra')
                    ->required()
                    ->numeric()
                    ->step('0.0001')
                    ->minValue(0)
                    ->suffix('DOP')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $buy = floatval($state);
                        $sell = floatval($get('sell_rate'));
                        if ($buy > 0 && $sell > 0) {
                            $set('average_rate', number_format(($buy + $sell) / 2, 4, '.', ''));
                        }
                    }),
                TextInput::make('sell_rate')
                    ->label('Tasa de Venta')
                    ->required()
                    ->numeric()
                    ->step('0.0001')
                    ->minValue(0)
                    ->suffix('DOP')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $buy = floatval($get('buy_rate'));
                        $sell = floatval($state);
                        if ($buy > 0 && $sell > 0) {
                            $set('average_rate', number_format(($buy + $sell) / 2, 4, '.', ''));
                        }
                    }),
                TextInput::make('average_rate')
                    ->label('Promedio')
                    ->numeric()
                    ->step('0.0001')
                    ->minValue(0)
                    ->suffix('DOP')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }
}
