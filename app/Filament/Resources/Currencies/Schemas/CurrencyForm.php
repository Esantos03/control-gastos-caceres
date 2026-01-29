<?php

namespace App\Filament\Resources\Currencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(3)
                    ->placeholder('USD'),
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Dólar Estadounidense'),
                TextInput::make('symbol')
                    ->label('Símbolo')
                    ->required()
                    ->maxLength(10)
                    ->placeholder('$'),
            ]);
    }
}
