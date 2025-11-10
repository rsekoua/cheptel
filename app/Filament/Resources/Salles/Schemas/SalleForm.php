<?php

namespace App\Filament\Resources\Salles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SalleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type_salle_id')
                    ->relationship('typeSalle', 'nom')
                    ->required(),
                TextInput::make('nom')
                    ->required(),
                TextInput::make('capacite')
                    ->required()
                    ->numeric(),
                TextInput::make('statut')
                    ->required()
                    ->default('disponible'),
                DatePicker::make('date_debut_vide_sanitaire'),
                TextInput::make('duree_vide_sanitaire_jours')
                    ->required()
                    ->numeric()
                    ->default(7),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
