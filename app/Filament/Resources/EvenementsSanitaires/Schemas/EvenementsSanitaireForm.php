<?php

namespace App\Filament\Resources\EvenementsSanitaires\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EvenementsSanitaireForm
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
                DateTimePicker::make('date_evenement')
                    ->required(),
                TextInput::make('type_evenement')
                    ->required(),
                Select::make('produit_sanitaire_id')
                    ->relationship('produitSanitaire', 'id'),
                TextInput::make('dose_administree')
                    ->numeric(),
                TextInput::make('nb_animaux_traites')
                    ->numeric(),
                TextInput::make('intervenant'),
                Textarea::make('motif')
                    ->columnSpanFull(),
                TextInput::make('cout_total')
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
