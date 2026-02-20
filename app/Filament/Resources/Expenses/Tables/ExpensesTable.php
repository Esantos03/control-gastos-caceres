<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->columns([
                TextColumn::make('expense_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(config('expenses.widgets.expense_table_limit')),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->formatStateUsing(function ($record) {
                        $currencyCode = $record->currency->code ?? 'USD';
                        $prefix = $currencyCode === 'USD' ? 'USD$' : 'DOP$';
                        return $prefix . number_format($record->amount, 2);
                    })
                    ->sortable(),
                TextColumn::make('currency.name')
                    ->label('Moneda')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('exchange_rate')
                    ->label('Tasa de Cambio')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_converted')
                    ->label('Monto Convertido')
                    ->money('DOP')
                    ->sortable()
                    ->state(function ($record) {
                        // Solo convertir si la moneda es USD
                        if ($record->currency->code !== 'USD') {
                            return null;
                        }
                        
                        // Obtener la tasa de cambio más reciente del dólar
                        $latestRate = \App\Models\ExchangeRate::whereHas('currency', function ($query) {
                            $query->where('code', 'USD');
                        })
                            ->orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        if (!$latestRate) {
                            return null;
                        }
                        
                        // Calcular con la tasa promedio actual
                        return round($record->amount * $latestRate->average_rate, 2);
                    })
                    ->placeholder('N/A'),
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->searchable(),
                TextColumn::make('subcategory.name')
                    ->label('Subcategoría')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                TextColumn::make('payment_detail')
                    ->label('Detalle de Pago')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->state(function ($record) {
                        // Si tiene tarjeta, mostrar tarjeta con últimos dígitos
                        if ($record->card) {
                            return $record->card->last_digits 
                                ? "{$record->card->name} ****{$record->card->last_digits}"
                                : $record->card->name;
                        }
                        
                        // Si tiene número de cheque, mostrarlo
                        if ($record->check_number) {
                            return "Cheque: {$record->check_number}";
                        }
                        
                        // Si no tiene ni tarjeta ni cheque, mostrar N/A
                        return 'N/A';
                    }),
                TextColumn::make('merchant.name')
                    ->label('Comercio')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ReplicateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('expense_date', 'desc');
    }
}
