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
            ->columns([
                TextColumn::make('expense_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money(fn ($record) => $record->currency->code ?? 'USD')
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
                    ->sortable(),
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
                TextColumn::make('card.name')
                    ->label('Tarjeta')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->toggleable(),
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
