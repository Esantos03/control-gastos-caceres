<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\ExchangeRate;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpensesStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Total de gastos del mes actual
        $totalExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->count();

        // Gastos por tarjetas del mes actual
        $expensesByCard = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->whereNotNull('card_id')
            ->with('card')
            ->get()
            ->groupBy('card_id')
            ->map(function ($expenses) {
                return [
                    'card' => $expenses->first()->card->name ?? 'Sin tarjeta',
                    'total' => $expenses->sum('amount_converted'),
                ];
            })
            ->sortByDesc('total')
            ->take(3);

        // Última tasa de cambio del dólar (la más reciente creada)
        $dollarRate = ExchangeRate::whereHas('currency', function ($query) {
            $query->where('code', 'USD');
        })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        $stats = [];

        // Stat: Total de gastos
        $stats[] = Stat::make('Total de Gastos', $totalExpenses)
            ->description('Gastos registrados este mes')
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('success');

        // Stat: Gastos por tarjetas
        if ($expensesByCard->isNotEmpty()) {
            $cardDescription = $expensesByCard->map(function ($item) {
                return $item['card'] . ': $' . number_format($item['total'], 2);
            })->join(' | ');

            $stats[] = Stat::make('Gastos por Tarjetas', '$' . number_format($expensesByCard->sum('total'), 2))
                ->description($cardDescription)
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning');
        }

        // Stat: Tasa del dólar (última registrada)
        if ($dollarRate) {
            $monthNames = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            $period = $monthNames[$dollarRate->month] . ' ' . $dollarRate->year;
            
            $stats[] = Stat::make('Tasa del Dólar', 'Compra: $' . number_format($dollarRate->buy_rate, 2))
                ->description('Venta: $' . number_format($dollarRate->sell_rate, 2) . ' (' . $period . ')')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info');
        }

        return $stats;
    }
}
