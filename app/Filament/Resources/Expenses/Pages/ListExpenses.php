<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Eager load de relaciones para optimizar consultas
     */
    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with([
                'currency',
                'category',
                'subcategory',
                'paymentMethod',
                'card',
                'merchant',
            ]);
    }
}
