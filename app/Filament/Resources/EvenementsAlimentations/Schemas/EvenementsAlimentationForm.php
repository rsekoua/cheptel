<?php

namespace App\Filament\Resources\EvenementsAlimentations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EvenementsAlimentationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lot_id')
                    ->relationship('lot', 'id'),
                Select::make('animal_id')
                    ->relationship('animal', 'id'),
                DatePicker::make('date_debut')
                    ->required(),
                DatePicker::make('date_fin')
                    ->required(),
                Select::make('plan_alimentation_id')
                    ->relationship('planAlimentation', 'id')
                    ->required(),
                TextInput::make('quantite_kg')
                    ->required()
                    ->numeric(),
                TextInput::make('nb_animaux')
                    ->numeric(),
                TextInput::make('cout_total')
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
