<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_expense')
                ->label('Crear Nuevo Gasto')
                ->url(ExpenseResource::getUrl('create')),
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\ExpensesStatsWidget::class,
            \App\Filament\Widgets\ExpensesTableWidget::class,
        ];
    }
}
