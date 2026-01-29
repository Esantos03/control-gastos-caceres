<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Expenses\ExpenseResource;
use App\Models\Expense;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ExpensesTableWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Expense::query()
                    ->with(['category', 'merchant', 'currency', 'paymentMethod', 'card'])
                    ->latest('expense_date')
            )
            ->defaultPaginationPageOption(10)
            ->recordUrl(
                fn ($record): string => ExpenseResource::getUrl('edit', ['record' => $record])
            )
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('merchant.name')
                    ->label('Comercio')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money(fn ($record) => $record->currency->code ?? 'USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_converted')
                    ->label('Monto Convertido')
                    ->money('DOP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('card.name')
                    ->label('Tarjeta')
                    ->badge()
                    ->color('warning')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('payment_method_id')
                    ->label('Método de Pago')
                    ->relationship('paymentMethod', 'name'),
            ]);
    }
}
