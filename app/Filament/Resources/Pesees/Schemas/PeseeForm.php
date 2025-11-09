<?php

namespace App\Filament\Resources\Pesees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PeseeForm
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
                DatePicker::make('date_pesee')
                    ->required(),
                TextInput::make('poids_total_kg')
                    ->required()
                    ->numeric(),
                TextInput::make('nb_animaux_peses')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('poids_moyen_kg')
                    ->required()
                    ->numeric(),
                TextInput::make('methode')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
