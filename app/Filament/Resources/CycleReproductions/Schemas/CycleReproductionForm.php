<?php

namespace App\Filament\Resources\CycleReproductions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CycleReproductionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->description('Informations de base sur le cycle de reproduction')
                    ->schema([
                        Select::make('animal_id')
                            ->label('Animal')
                            ->relationship('animal', 'numero_identification')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Animal concerné par ce cycle de reproduction (truie ou cochette)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('numero_cycle')
                            ->label('Numéro de cycle')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro séquentiel du cycle pour cet animal (1 = premier cycle, 2 = deuxième cycle, etc.)')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_debut')
                            ->label('Date de début')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de début du cycle de reproduction')
                                    ->color('gray'),
                            ])),

                        Select::make('statut_cycle')
                            ->label('Statut du cycle')
                            ->required()
                            ->options([
                                'en_cours' => 'En cours',
                                'termine_succes' => 'Terminé avec succès',
                                'termine_echec' => 'Terminé en échec',
                                'avorte' => 'Avorté',
                            ])
                            ->default('en_cours')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Statut actuel du cycle de reproduction')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Chaleurs et saillie')
                    ->description('Informations sur les chaleurs et l\'insémination/saillie')
                    ->schema([
                        DateTimePicker::make('date_chaleurs')
                            ->label('Date des chaleurs')
                            ->native(false)
                            ->seconds(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date et heure d\'observation des chaleurs de l\'animal')
                                    ->color('gray'),
                            ])),

                        DateTimePicker::make('date_premiere_saillie')
                            ->label('Date de première saillie')
                            ->native(false)
                            ->seconds(false)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                // Calculer automatiquement la date de mise-bas prévue (114 jours après la saillie)
                                if ($state) {
                                    $dateSaillie = \Carbon\Carbon::parse($state);
                                    $dateMiseBasPrevue = $dateSaillie->addDays(114);
                                    $set('date_mise_bas_prevue', $dateMiseBasPrevue->format('Y-m-d'));
                                }
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date et heure de la première insémination ou saillie')
                                    ->color('gray'),
                            ])),

                        Select::make('type_saillie')
                            ->label('Type de saillie')
                            ->options([
                                'IA' => 'Insémination Artificielle (IA)',
                                'MN' => 'Monte Naturelle (MN)',
                            ])
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('IA = Insémination Artificielle, MN = Monte Naturelle (accouplement naturel avec un verrat)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(3),

                Section::make('Diagnostic de gestation')
                    ->description('Résultats du diagnostic de gestation')
                    ->schema([
                        DatePicker::make('date_diagnostic')
                            ->label('Date du diagnostic')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date du diagnostic de gestation (généralement 21-28 jours après la saillie)')
                                    ->color('gray'),
                            ])),

                        Select::make('resultat_diagnostic')
                            ->label('Résultat du diagnostic')
                            ->required()
                            ->options([
                                'en_attente' => 'En attente',
                                'positif' => 'Positif (gestante)',
                                'negatif' => 'Négatif (vide)',
                            ])
                            ->default('en_attente')
                            ->native(false)
                            ->live()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Résultat du diagnostic de gestation')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Mise-bas')
                    ->description('Dates prévisionnelle et réelle de mise-bas')
                    ->schema([
                        DatePicker::make('date_mise_bas_prevue')
                            ->label('Date de mise-bas prévue')
                            ->native(false)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Calculée automatiquement : date de saillie + 114 jours')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de mise-bas prévue (calculée automatiquement : date saillie + 114 jours de gestation)')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_mise_bas_reelle')
                            ->label('Date de mise-bas réelle')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date réelle de mise-bas (à remplir après la mise-bas)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Échec et notes')
                    ->description('Informations en cas d\'échec et notes complémentaires')
                    ->schema([
                        Textarea::make('motif_echec')
                            ->label('Motif d\'échec')
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn ($get) => in_array($get('statut_cycle'), ['termine_echec', 'avorte']))
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Raison de l\'échec ou de l\'avortement du cycle (ex: non fécondation, avortement, pathologie)')
                                    ->color('gray'),
                            ])),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Observations complémentaires sur le cycle de reproduction')
                                    ->color('gray'),
                            ])),
                    ])
                    ->collapsed(),
            ]);
    }
}
