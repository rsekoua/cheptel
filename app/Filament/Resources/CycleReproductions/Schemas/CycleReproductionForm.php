<?php

namespace App\Filament\Resources\CycleReproductions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
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
                    ->description('Les cycles sont créés automatiquement quand l\'animal passe au statut "Sevrée" ou "En chaleurs"')
                    ->schema([
                        Select::make('animal_id')
                            ->label('Animal')
                            ->relationship('animal', 'numero_identification')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Animal concerné par ce cycle (généré automatiquement)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('numero_cycle')
                            ->label('Numéro de cycle')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro séquentiel calculé automatiquement (1 = premier cycle, 2 = deuxième, etc.)')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_debut')
                            ->label('Date de début')
                            ->native(false)
                            ->disabled()
                            ->dehydrated()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de début du cycle (générée automatiquement)')
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
                        DateTimePicker::make('date_chaleurs')
                            ->label('Date des chaleurs')
                            ->native(false)
                            ->seconds(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date et heure d\'observation des chaleurs de l\'animal')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(3),

                Section::make('Saillies / Inséminations')
                    ->description('Enregistrement des multiples inséminations ou saillies (recommandé : 2-3 inséminations à 12-24h d\'intervalle)')
                    ->schema([
                        Repeater::make('saillies')
                            ->label('Liste des saillies')
                            ->relationship()
                            ->schema([
                                Select::make('type')
                                    ->label('Type')
                                    ->required()
                                    ->options([
                                        'IA' => 'Insémination Artificielle',
                                        'MN' => 'Monte Naturelle',
                                    ])
                                    ->default('IA')
                                    ->native(false)
                                    ->live()
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('IA = Insémination Artificielle, MN = Monte Naturelle (avec verrat)')
                                            ->color('gray'),
                                    ])),

                                DateTimePicker::make('date_heure')
                                    ->label('Date et heure')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->default(now())
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Calculer la date de mise-bas prévue basée sur la PREMIÈRE saillie
                                        $saillies = $get('../../saillies') ?? [];
                                        if (! empty($saillies)) {
                                            // Trouver la date de saillie la plus ancienne
                                            $dates = array_filter(array_column($saillies, 'date_heure'));
                                            if (! empty($dates)) {
                                                $premiereDate = min($dates);
                                                $dateSaillie = \Carbon\Carbon::parse($premiereDate);
                                                $dateMiseBasPrevue = $dateSaillie->copy()->addDays(114);
                                                $set('../../date_mise_bas_prevue', $dateMiseBasPrevue->format('Y-m-d'));
                                            }
                                        }
                                    })
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Date et heure de cette insémination ou saillie')
                                            ->color('gray'),
                                    ])),

                                Select::make('verrat_id')
                                    ->label('Verrat')
                                    ->options(function () {
                                        return \App\Models\Animal::where('type_animal', 'verrat')
                                            ->pluck('numero_identification', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('type') === 'MN')
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Verrat utilisé pour la monte naturelle (seulement pour MN)')
                                            ->color('gray'),
                                    ])),

                                TextInput::make('semence_lot_numero')
                                    ->label('N° lot de semence')
                                    ->maxLength(100)
                                    ->visible(fn ($get) => $get('type') === 'IA')
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Numéro du lot de semence utilisé pour l\'IA')
                                            ->color('gray'),
                                    ])),

                                TextInput::make('intervenant')
                                    ->label('Intervenant')
                                    ->maxLength(100)
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Nom de la personne ayant effectué l\'insémination ou la saillie')
                                            ->color('gray'),
                                    ])),

                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(2)
                                    ->columnSpanFull()
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Observations particulières sur cette saillie')
                                            ->color('gray'),
                                    ])),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => isset($state['date_heure']) ? \Carbon\Carbon::parse($state['date_heure'])->format('d/m/Y H:i').' - '.($state['type'] ?? '') : null)
//                            ->itemLabel(fn (array $state): ?string => isset($state['date_heure']) ? \Carbon\Carbon::parse($state['date_heure'])->format('d/m/Y H:i').' - '.($state['type'] ?? '') : null)
                            ->addActionLabel('Ajouter une saillie / insémination')
                            ->reorderable(false)
                            ->collapsible()
                            ->defaultItems(0)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Ajoutez plusieurs saillies/inséminations pour maximiser les chances de réussite (généralement 2-3 à 12-24h d\'intervalle)')
                                    ->color('gray'),
                            ])),
                    ]),

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
