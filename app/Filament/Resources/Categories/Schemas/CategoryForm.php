<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                
                ColorPicker::make('color')
                    ->label('Color')
                    ->default('#3B82F6')
                    ->helperText('Color para identificar visualmente la categoría'),
                
                Select::make('icon')
                    ->label('Icono')
                    ->options([
                        'tag' => 'Etiqueta',
                        'shopping-cart' => 'Carrito de Compras',
                        'home' => 'Casa',
                        'currency-dollar' => 'Dinero',
                        'credit-card' => 'Tarjeta',
                        'truck' => 'Transporte',
                        'academic-cap' => 'Educación',
                        'heart' => 'Salud',
                        'film' => 'Entretenimiento',
                        'phone' => 'Teléfono',
                        'lightning-bolt' => 'Servicios',
                        'gift' => 'Regalos',
                        'briefcase' => 'Trabajo',
                        'cake' => 'Alimentación',
                        'sparkles' => 'Belleza',
                        'wrench' => 'Mantenimiento',
                        'globe' => 'Viajes',
                        'book-open' => 'Libros',
                        'music-note' => 'Música',
                        'puzzle' => 'Hobbies',
                    ])
                    ->default('tag')
                    ->searchable(),
                
                TextInput::make('monthly_budget')
                    ->label('Presupuesto Mensual')
                    ->numeric()
                    ->step('0.01')
                    ->minValue(0)
                    ->prefix('RD$')
                    ->helperText('Presupuesto mensual asignado a esta categoría'),
                
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->helperText('Orden de visualización (menor número aparece primero)'),
                
                Toggle::make('is_active')
                    ->label('¿Activa?')
                    ->default(true)
                    ->inline(false)
                    ->helperText('Si la categoría está activa y disponible para usar'),
            ]);
    }
}
