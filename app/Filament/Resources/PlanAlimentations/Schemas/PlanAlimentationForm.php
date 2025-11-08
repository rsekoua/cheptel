<?php

namespace App\Filament\Resources\PlanAlimentations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanAlimentationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Nom du plan (ex: Flushing, Gestation Phase 1, Lactation, Post-Sevrage 1er Âge)'),

                Select::make('type_animal')
                    ->required()
                    ->options([
                        'reproducteur' => 'Reproducteur',
                        'production' => 'Production',
                    ])
                    ->native(false)
                    ->helperText('Reproducteur = truies/verrats. Production = lots de porcelets/porcs'),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->helperText('Objectifs nutritionnels et période d\'application du plan'),

                TextInput::make('energie_mcal_jour')
                    ->numeric()
                    ->suffix('Mcal/jour')
                    ->step(0.01)
                    ->helperText('Apport énergétique quotidien en mégacalories'),

                TextInput::make('proteine_pourcent')
                    ->numeric()
                    ->suffix('%')
                    ->step(0.1)
                    ->helperText('Pourcentage de protéines dans l\'aliment (généralement 14-18%)'),

                TextInput::make('ration_kg_jour')
                    ->numeric()
                    ->suffix('kg/jour')
                    ->step(0.01)
                    ->helperText('Quantité d\'aliment par jour et par animal (laisser vide si à volonté)'),

                Toggle::make('a_volonte')
                    ->helperText('Cocher si l\'alimentation est distribuée à volonté (ex: lactation)')
            ]);
    }
}
