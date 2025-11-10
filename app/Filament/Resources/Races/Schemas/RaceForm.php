<?php

namespace App\Filament\Resources\Races\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class RaceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->required()
                    ->maxLength(100)
                    ->afterLabel(Schema::start([
                        Icon::make(Heroicon::QuestionMarkCircle)
                            ->tooltip('Nom de la race (ex: Large White, Landrace, Piétrain, Duroc)')
                            ->color('gray'),
                    ])),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->afterLabel(Schema::start([
                        Icon::make(Heroicon::QuestionMarkCircle)
                            ->tooltip('Caractéristiques génétiques et aptitudes de la race')
                            ->color('gray'),
                    ])),

                Select::make('type')
                    ->required()
                    ->options([
                        'maternelle' => 'Maternelle',
                        'paternelle' => 'Paternelle',
                        'mixte' => 'Mixte',
                    ])
                    ->native(false)
                    ->afterLabel(Schema::start([
                        Icon::make(Heroicon::QuestionMarkCircle)
                            ->tooltip('Maternelle = prolificité/qualités maternelles. Paternelle = qualité de viande. Mixte = usage multiple')
                            ->color('gray'),
                    ])),

                TextInput::make('gmq_moyen')
                    ->numeric()
                    ->suffix('g/jour')
                    ->afterLabel(Schema::start([
                        Icon::make(Heroicon::QuestionMarkCircle)
                            ->tooltip('Gain Moyen Quotidien attendu en grammes par jour (généralement 700-850 g/j)')
                            ->color('gray'),
                    ])),

                TextInput::make('poids_adulte_moyen')
                    ->numeric()
                    ->suffix('kg')
                    ->afterLabel(Schema::start([
                        Icon::make(Heroicon::QuestionMarkCircle)
                            ->tooltip('Poids moyen à l\'âge adulte en kilogrammes (généralement 250-300 kg)')
                            ->color('gray'),
                    ])),
            ]);
    }
}
