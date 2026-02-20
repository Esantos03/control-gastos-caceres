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

    protected ?string $pollingInterval = null;

    public function __construct()
    {
        $this->pollingInterval = config('expenses.widgets.polling_interval');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Expense::query()
                    ->with(['category', 'merchant', 'currency', 'paymentMethod', 'card'])
                    ->latest()
            )
            ->defaultPaginationPageOption(10)
            ->defaultSort('created_at', 'desc')
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
                    ->limit(config('expenses.widgets.table_description_limit')),
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
                    ->formatStateUsing(function ($record) {
                        $currencyCode = $record->currency->code ?? 'USD';
                        $prefix = $currencyCode === 'USD' ? 'USD$' : 'DOP$';
                        return $prefix . number_format($record->amount, 2);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_converted')
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
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('payment_detail')
                    ->label('Detalle de Pago')
                    ->badge()
                    ->color('warning')
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
