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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CycleReproductionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informations du cycle')
                    ->description('Informations générales sur le cycle de reproduction')
                    ->icon(Heroicon::ArrowPath)
                    ->schema([
                        Select::make('animal_id')
                            ->label('Animal')
                            ->relationship('animal', 'numero_identification')
                            ->searchable()
                            ->preload()
//                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Animal concerné par ce cycle (généré automatiquement)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('numero_cycle')
                            ->label('Numéro de cycle')
                            ->numeric()
                            ->required()
//                            ->disabled()
                            ->dehydrated()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro séquentiel calculé automatiquement')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_debut')
                            ->label('Date de début du cycle')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->maxDate(now())
                            ->helperText('Générée automatiquement, mais modifiable')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de début du cycle de reproduction')
                                    ->color('gray'),
                            ])),

                        Select::make('statut_cycle')
                            ->label('Statut du cycle')
                            ->required()
                            ->options([
                                'en_attente' => 'En attente',
                                'gestation_en_cours' => 'Gestation en cours',
                                'en_lactation' => 'En lacation',
                                'termine_succes' => 'Terminé avec succes',
                                'termine_echec' => 'Terminé avec echec',
                            ])
                            ->default('en_attente')
                            ->native(false)
                            ->live()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Statut actuel du cycle de reproduction')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                Section::make('Saillies / Inséminations')
                    ->description('Enregistrement des multiples inséminations ou saillies (recommandé : 2-3 inséminations à 12-24h d\'intervalle)')
                    ->icon(Heroicon::Heart)
                    ->schema([
                        Repeater::make('saillies')
                            ->label('Liste des saillies')
                            ->relationship()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $saillies = $state ?? [];
                                $dates = array_filter(array_column($saillies, 'date_heure'));

                                if (! empty($dates)) {
                                    $premiereDate = min($dates);
                                    $dateSaillie = \Carbon\Carbon::parse($premiereDate);
                                    $dateMiseBasPrevue = $dateSaillie->copy()->addDays(114);
                                    $set('../date_mise_bas_prevue', $dateMiseBasPrevue->format('Y-m-d'));
                                    $set('../date_premiere_saillie', $premiereDate);

                                    // Détecter le type de saillie le plus utilisé
                                    $types = array_filter(array_column($saillies, 'type'));
                                    if (! empty($types)) {
                                        $typePrincipal = array_count_values($types);
                                        arsort($typePrincipal);
                                        $set('../type_saillie', array_key_first($typePrincipal));
                                    }
                                } else {
                                    $set('../date_mise_bas_prevue', null);
                                    $set('../date_premiere_saillie', null);
                                    $set('../type_saillie', null);
                                }
                            })
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
                                            ->tooltip('IA = Insémination Artificielle, MN = Monte Naturelle')
                                            ->color('gray'),
                                    ])),

                                DateTimePicker::make('date_heure')
                                    ->label('Date et heure')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->maxDate(now())
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $saillies = $get('../../saillies') ?? [];
                                        $dates = array_filter(array_column($saillies, 'date_heure'));

                                        if (! empty($dates)) {
                                            $premiereDate = min($dates);
                                            $dateSaillie = \Carbon\Carbon::parse($premiereDate);
                                            $dateMiseBasPrevue = $dateSaillie->copy()->addDays(114);
                                            $set('../../date_mise_bas_prevue', $dateMiseBasPrevue->format('Y-m-d'));
                                            $set('../../date_premiere_saillie', $premiereDate);

                                            // Détecter le type de saillie le plus utilisé
                                            $types = array_filter(array_column($saillies, 'type'));
                                            if (! empty($types)) {
                                                $typePrincipal = array_count_values($types);
                                                arsort($typePrincipal);
                                                $set('../../type_saillie', array_key_first($typePrincipal));
                                            }
                                        } else {
                                            $set('../../date_mise_bas_prevue', null);
                                            $set('../../date_premiere_saillie', null);
                                            $set('../../type_saillie', null);
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
                                    ->native(false)
                                    ->visible(fn ($get) => $get('type') === 'MN')
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Verrat utilisé pour la monte naturelle')
                                            ->color('gray'),
                                    ])),

                                TextInput::make('semence_lot_numero')
                                    ->label('N° lot de semence')
                                    ->maxLength(100)
                                    ->visible(fn ($get) => $get('type') === 'IA')
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Numéro du lot de semence utilisé')
                                            ->color('gray'),
                                    ])),
                                //
                                //                                TextInput::make('intervenant')
                                //                                    ->label('Intervenant')
                                //                                    ->maxLength(100)
                                //                                    ->afterLabel(Schema::start([
                                //                                        Icon::make(Heroicon::QuestionMarkCircle)
                                //                                            ->tooltip('Personne ayant effectué l\'insémination')
                                //                                            ->color('gray'),
                                //                                    ])),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => isset($state['date_heure'])
                                ? \Carbon\Carbon::parse($state['date_heure'])->format('d/m/Y H:i').' - '.($state['type'] ?? '')
                                : null)
                            ->deleteAction(
                                fn ($action) => $action->after(function ($get, $set) {
                                    $saillies = $get('saillies') ?? [];
                                    $dates = array_filter(array_column($saillies, 'date_heure'));

                                    if (! empty($dates)) {
                                        $premiereDate = min($dates);
                                        $dateSaillie = \Carbon\Carbon::parse($premiereDate);
                                        $dateMiseBasPrevue = $dateSaillie->copy()->addDays(114);
                                        $set('date_mise_bas_prevue', $dateMiseBasPrevue->format('Y-m-d'));
                                        $set('date_premiere_saillie', $premiereDate);

                                        $types = array_filter(array_column($saillies, 'type'));
                                        if (! empty($types)) {
                                            $typePrincipal = array_count_values($types);
                                            arsort($typePrincipal);
                                            $set('type_saillie', array_key_first($typePrincipal));
                                        }
                                    } else {
                                        $set('date_mise_bas_prevue', null);
                                        $set('date_premiere_saillie', null);
                                        $set('type_saillie', null);
                                    }
                                })
                            )
                            ->addActionLabel('Ajouter une saillie / insémination')
                            ->reorderable(false)
                            ->collapsible()
                            ->defaultItems(0)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Ajoutez plusieurs saillies/inséminations pour maximiser les chances (2-3 à 12-24h d\'intervalle)')
                                    ->color('gray'),
                            ])),

                        // Champs cachés pour stocker date_premiere_saillie et type_saillie
                        DateTimePicker::make('date_premiere_saillie')
                            ->label('Date première saillie')
                            ->hidden()
                            ->dehydrated(),

                        Select::make('type_saillie')
                            ->label('Type saillie')
                            ->options([
                                'IA' => 'IA',
                                'MN' => 'MN',
                            ])
                            ->hidden()
                            ->dehydrated(),
                    ])
                    ->columnSpan(2),

                Section::make('Diagnostic de gestation')
                    ->description('Résultats du diagnostic de gestation')
                    ->icon(Heroicon::Beaker)
                    ->schema([
                        DatePicker::make('date_diagnostic')
                            ->label('Date du diagnostic')
                            ->native(false)
                            ->maxDate(now())
                            ->minDate(fn (Get $get): ?string => $get('date_premiere_saillie'))
                            ->helperText('Doit être après la première saillie')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date du diagnostic de gestation')
                                    ->color('gray'),
                            ])),

                        Select::make('resultat_diagnostic')
                            ->label('Résultat du diagnostic')
                            ->required()
                            ->options([
                                'positif' => 'Positif (gestante)',
                                'negatif' => 'Négatif (vide)',
                            ])
                            ->default('positif')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Résultat du diagnostic de gestation')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->visible(fn (Get $get, $record): bool => ! empty($get('date_premiere_saillie')) || ($record && ! empty($record->date_premiere_saillie))),

                Section::make('Mise-bas')
                    ->description('Dates prévisionnelle et réelle de mise-bas')
                    ->icon(Heroicon::Cake)
                    ->schema([
                        DatePicker::make('date_mise_bas_prevue')
                            ->label('Date de mise-bas prévue')
                            ->native(false)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Calculée automatiquement (saillie + 114 jours)')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de mise-bas prévue (calculée automatiquement)')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_mise_bas_reelle')
                            ->label('Date de mise-bas réelle')
                            ->native(false)
                            ->maxDate(now())
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date réelle de mise-bas (à remplir après la mise-bas)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->visible(fn (Get $get, $record): bool => ! empty($get('date_premiere_saillie')) || ($record && ! empty($record->date_premiere_saillie))),

                Section::make('Informations complémentaires')
                    ->description('Motif d\'échec et notes')
                    ->icon(Heroicon::DocumentText)
                    ->schema([
                        Textarea::make('motif_echec')
                            ->label('Motif d\'échec')
                            ->placeholder('Raison de l\'échec de gestation...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('statut_cycle') === 'echec_gestation')
                            ->required(fn (Get $get): bool => $get('statut_cycle') === 'echec_gestation')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Raison de l\'échec de gestation')
                                    ->color('gray'),
                            ])),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Observations complémentaires sur le cycle...')
                            ->rows(4)
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Observations complémentaires')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columnSpan(2)
                    ->collapsed(),
            ]);
    }
}
