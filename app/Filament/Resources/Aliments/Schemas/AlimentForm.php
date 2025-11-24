<?php

namespace App\Filament\Resources\Aliments\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AlimentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informations générales')
                    ->description('Identification et description de l\'aliment')
                    ->schema([
                        TextInput::make('nom')
                            ->label('Nom de l\'aliment')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Maïs, Tourteau de soja, Son de blé...')
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nom de l\'aliment utilisé dans l\'élevage')
                                    ->color('gray'),
                            ])),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->placeholder('Description détaillée, composition, origine, caractéristiques nutritionnelles...')
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Informations détaillées sur l\'aliment et ses caractéristiques')
                                    ->color('gray'),
                            ])),
                    ]),

                Section::make('Paramètres')
                    ->description('Configuration du conditionnement et des alertes de stock')
                    ->columns(2)
                    ->schema([
                        TextInput::make('poids_sac_standard')
                            ->label('Poids d\'un sac standard')
                            ->required()
                            ->numeric()
                            ->suffix('kg')
                            ->default(50)
                            ->minValue(0.01)
                            ->step(0.01)
                            ->helperText('Utilisé pour les conversions sacs ↔ kilogrammes')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids standard d\'un sac de cet aliment en kilogrammes')
                                    ->color('gray'),
                            ])),

                        TextInput::make('seuil_alerte')
                            ->label('Seuil d\'alerte stock')
                            ->required()
                            ->numeric()
                            ->suffix('kg')
                            ->default(100)
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Vous serez alerté si le stock entrepôt descend sous ce seuil')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Quantité minimale en stock avant déclenchement d\'une alerte')
                                    ->color('gray'),
                            ])),
                    ]),

                Section::make('Statut')
                    ->description('Activation ou désactivation de l\'aliment')
                    ->schema([
                        Toggle::make('actif')
                            ->label('Aliment actif')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Désactivez un aliment s\'il n\'est plus utilisé dans l\'élevage')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Les aliments inactifs n\'apparaîtront plus dans les listes de sélection')
                                    ->color('gray'),
                            ])),
                    ]),
            ]);
    }
}
