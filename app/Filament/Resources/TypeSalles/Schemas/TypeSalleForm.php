<?php

namespace App\Filament\Resources\TypeSalles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TypeSalleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Nom du type de salle (ex: Maternité, Post-Sevrage, Engraissement, Gestantes, Verraterie)'),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->helperText('Description détaillée des caractéristiques et spécificités de ce type de salle'),
                Select::make('capacite_type')
                    ->required()
                    ->options([
                        'individuelle' => 'Individuelle',
                        'collective' => 'Collective',
                    ])
                    ->native(false)
                    ->helperText('Individuelle = cases séparées (1 animal/case). Collective = parcs regroupant plusieurs animaux'),
                TextInput::make('temperature_optimale')
                    ->numeric()
                    ->suffix('°C')
                    ->step(0.1)
                    ->helperText('Température recommandée pour ce type de salle (Maternité: 21-22°C, Post-Sevrage: 26°C, Engraissement: 20°C)'),
            ]);
    }
}
