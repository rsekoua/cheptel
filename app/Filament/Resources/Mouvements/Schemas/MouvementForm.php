<?php

namespace App\Filament\Resources\Mouvements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MouvementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('type_cible')
                    ->required(),
                Select::make('animal_id')
                    ->relationship('animal', 'id'),
                Select::make('lot_id')
                    ->relationship('lot', 'id'),
                DateTimePicker::make('date_mouvement')
                    ->required(),
                Select::make('salle_origine_id')
                    ->relationship('salleOrigine', 'id'),
                Select::make('salle_destination_id')
                    ->relationship('salleDestination', 'id')
                    ->required(),
                TextInput::make('place_numero'),
                TextInput::make('motif')
                    ->required(),
                TextInput::make('nb_animaux')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
