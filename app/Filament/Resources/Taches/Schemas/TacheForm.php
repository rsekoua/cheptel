<?php

namespace App\Filament\Resources\Taches\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TacheForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titre')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('type_tache')
                    ->required(),
                TextInput::make('priorite')
                    ->required()
                    ->default('normale'),
                TextInput::make('type_cible')
                    ->required(),
                Select::make('animal_id')
                    ->relationship('animal', 'id'),
                Select::make('lot_id')
                    ->relationship('lot', 'id'),
                Select::make('portee_id')
                    ->relationship('portee', 'id'),
                Select::make('salle_id')
                    ->relationship('salle', 'id'),
                DatePicker::make('date_echeance')
                    ->required(),
                DatePicker::make('date_debut_periode'),
                TextInput::make('statut')
                    ->required()
                    ->default('en_attente'),
                DateTimePicker::make('date_realisation'),
                Select::make('utilisateur_assigne_id')
                    ->relationship('utilisateurAssigne', 'name'),
                Select::make('utilisateur_realisation_id')
                    ->relationship('utilisateurRealisation', 'name'),
                Toggle::make('generee_automatiquement')
                    ->required(),
                TextInput::make('evenement_lie_type'),
                TextInput::make('evenement_lie_id')
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
