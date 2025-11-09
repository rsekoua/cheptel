<?php

namespace App\Filament\Resources\Portees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PorteeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->description('Il est recommandé d\'utiliser l\'action "Enregistrer la mise-bas" depuis le cycle de reproduction')
                    ->schema([
                        Select::make('cycle_reproduction_id')
                            ->label('Cycle de reproduction')
                            ->relationship('cycleReproduction', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Cycle #{$record->numero_cycle} - {$record->animal->numero_identification}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Cycle de reproduction ayant donné lieu à cette portée')
                                    ->color('gray'),
                            ])),

                        Select::make('animal_id')
                            ->label('Animal (Truie/Cochette)')
                            ->relationship('animal', 'numero_identification')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Truie ou cochette ayant mis bas')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Mise-bas')
                    ->description('Données de la mise-bas (naissance des porcelets)')
                    ->schema([
                        DatePicker::make('date_mise_bas')
                            ->label('Date et heure de mise-bas')
                            ->required()
                            ->native(false)
//                            ->seconds(false)
                            ->default(now())
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date et heure de la mise-bas')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_nes_vifs')
                            ->label('Nombre de nés vivants')
                             ->required()
                            ->numeric()
                             ->minValue(0)
                            ->suffix(' porcelets')
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $nbMortNes = $get('nb_mort_nes') ?? 0;
                                $nbMomifies = $get('nb_momifies') ?? 0;
                                $total = ($state ?? 0) - $nbMortNes - $nbMomifies;
                                $set('nb_total', $total);
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre de porcelets nés vivants')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_mort_nes')
                            ->label('Nombre de mort-nés')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix(' porcelets')
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $nbNesVifs = $get('nb_nes_vifs') ?? 0;
                                $nbMomifies = $get('nb_momifies') ?? 0;
                                $total = $nbNesVifs - ($state ?? 0) - $nbMomifies;
                                $set('nb_total', $total);
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre de porcelets mort-nés (morts pendant ou juste après la mise-bas)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_momifies')
                            ->label('Nombre de momifiés')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix('porcelets')
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $nbNesVifs = $get('nb_nes_vifs') ?? 0;
                                $nbMortNes = $get('nb_mort_nes') ?? 0;
                                $total = $nbNesVifs - $nbMortNes - ($state ?? 0);
                                $set('nb_total', $total);
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre de porcelets momifiés (morts in utero avant la mise-bas)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_total')
                            ->label('Nombre total vivant')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->suffix(' porcelets')
                            // ->helperText('Calculé automatiquement : Nés vivants + Mort-nés + Momifiés')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre total de porcelets (vivants - mort-nés - momifiés)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_moyen_naissance_g')
                            ->label('Poids moyen à la naissance')
                            ->numeric()
                            ->suffix('g')
                            ->step(1)
                            ->minValue(0)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids moyen des porcelets à la naissance en grammes (généralement entre 1200g et 1600g)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(3),

                Section::make('Sevrage')
                    ->description('Données du sevrage de la portée')
                    ->schema([
                        DatePicker::make('date_sevrage')
                            ->label('Date de sevrage')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date du sevrage (généralement 21-28 jours après la mise-bas)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_sevres')
                            ->label('Nombre de sevrés')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('porcelets')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre de porcelets sevrés vivants')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_total_sevrage_kg')
                            ->label('Poids total au sevrage')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01)
                            ->minValue(0)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids total cumulé de tous les porcelets sevrés')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_moyen_sevrage_kg')
                            ->label('Poids moyen au sevrage')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01)
                            ->minValue(0)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids moyen par porcelet au sevrage (généralement entre 6 et 8 kg)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Destination simple')
                    ->description('Si toute la portée va dans un seul lot (méthode simple)')
                    ->schema([
                        Select::make('lot_destination_id')
                            ->label('Lot de destination unique')
                            ->relationship('lotDestination', 'numero_lot')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Utilisez cette option si tous les porcelets vont dans le même lot')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Lot de post-sevrage où TOUS les porcelets seront transférés ensemble')
                                    ->color('gray'),
                            ])),
                    ]),

                Section::make('Répartition avancée dans plusieurs lots')
                    ->description('Si vous répartissez les porcelets dans plusieurs lots (tri par poids, qualité, etc.)')
                    ->schema([
                        Repeater::make('lots')
                            ->label('Répartition dans les lots')
                            ->relationship()
                            ->schema([
                                Select::make('id')
                                    ->label('Lot')
                                    ->options(function () {
                                        return \App\Models\Lot::query()
                                            ->where('statut_lot', 'actif')
                                            ->pluck('numero_lot', 'id');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->native(false)
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Lot de destination pour cette partie de la portée')
                                            ->color('gray'),
                                    ])),

                                TextInput::make('pivot.nb_porcelets_transferes')
                                    ->label('Nombre de porcelets')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('porcelets')
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Nombre de porcelets transférés dans ce lot')
                                            ->color('gray'),
                                    ])),

                                TextInput::make('pivot.poids_total_transfere_kg')
                                    ->label('Poids total transféré')
                                    ->numeric()
                                    ->suffix('kg')
                                    ->step(0.01)
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::QuestionMarkCircle)
                                            ->tooltip('Poids total des porcelets transférés dans ce lot')
                                            ->color('gray'),
                                    ])),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => isset($state['pivot']['nb_porcelets_transferes'])
                                ? "{$state['pivot']['nb_porcelets_transferes']} porcelets"
                                : null)
                            ->addActionLabel('Ajouter un lot de destination')
                            ->reorderable(false)
                            ->collapsible()
                            ->defaultItems(0)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Répartissez les porcelets sevrés dans différents lots selon leur poids, qualité, etc.')
                                    ->color('gray'),
                            ]))
                            ->helperText('⚠️ Si vous utilisez cette méthode, ne remplissez pas le "Lot de destination unique" ci-dessus'),
                    ])
                    ->collapsed(),

                Section::make('Notes')
                    ->description('Observations complémentaires')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Observations sur la mise-bas, problèmes rencontrés, interventions particulières, etc.')
                                    ->color('gray'),
                            ])),
                    ])
                    ->collapsed(),
            ]);
    }
}
