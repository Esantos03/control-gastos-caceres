<?php

namespace App\Filament\Resources\Cards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_digits')
                    ->label('Últimos Dígitos')
                    ->required()
                    ->maxLength(4)
                    ->numeric(),
                Select::make('type')
                    ->label('Tipo')
                    ->options(['credito' => 'Crédito', 'debito' => 'Débito'])
                    ->required(),
            ]);
    }
}
