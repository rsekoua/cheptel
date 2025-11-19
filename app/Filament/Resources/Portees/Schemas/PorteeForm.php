<?php

namespace App\Filament\Resources\Portees\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PorteeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informations générales')
                    ->description('Il est recommandé d\'utiliser l\'action "Mise bas" depuis le cycle de reproduction')
                    ->icon(Heroicon::InformationCircle)
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
                    ->columns(2)
                    ->columnSpan(1),

                Section::make('Mise bas')
                    ->description('Données de la mise bas (naissance des porcelets)')
                    ->icon(Heroicon::Cake)
                    ->schema([
                        DateTimePicker::make('date_mise_bas')
                            ->label('Date et heure de mise bas')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->maxDate(now())
                            ->default(now())
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date et heure de la mise bas')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_nes_vifs')
                            ->label('Nombre de nés vivants')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix(' porcelets')
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $nbNesVifs = (int) ($state ?? 0);
                                $nbMortNes = (int) ($get('nb_mort_nes') ?? 0);
                                $set('nb_total', $nbNesVifs - $nbMortNes);
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
                                $nbMortNes = (int) ($state ?? 0);
                                $nbNesVifs = (int) ($get('nb_nes_vifs') ?? 0);
                                $set('nb_total', $nbNesVifs - $nbMortNes);
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre de porcelets mort-nés')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_total')
                            ->label('Nombre total de porcelets vivants')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->default(0)
                            ->suffix(' porcelets')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Calculé automatiquement : Nés vivants - Mort-nés')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_moyen_naissance_g')
                            ->label('Poids moyen à la naissance')
                            ->numeric()
                            ->suffix('g')
                            ->step(1)
                            ->minValue(0)
                            ->helperText('Généralement entre 1200g et 1600g')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids moyen des porcelets à la naissance en grammes')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(3)
                    ->columnSpan(2),

                Section::make('Sevrage')
                    ->description('Données du sevrage de la portée')
                    ->icon(Heroicon::ArrowRightCircle)
                    ->schema([
                        DateTimePicker::make('date_sevrage')
                            ->label('Date de sevrage')
                            ->native(false)
                            ->seconds(false)
                            ->maxDate(now())
                            ->live()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date du sevrage (généralement 21-28 jours après la mise bas)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_sevres')
                            ->label('Nombre de sevrés')
                            ->numeric()
                            ->minValue(0)
                            ->suffix(' porcelets')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre de porcelets sevrés vivants')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_total_sevrage_kg')
                            ->label('Poids total au sevrage')
                            ->numeric()
                            ->suffix(' kg')
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
                            ->suffix(' kg')
                            ->step(0.01)
                            ->minValue(0)
                            ->helperText('Généralement entre 6 et 8 kg')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids moyen par porcelet au sevrage')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2)
                    ->columnSpan(1),

                Section::make('Destination')
                    ->description('Lot de destination après sevrage')
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->schema([
                        Select::make('lot_destination_id')
                            ->label('Lot de destination')
                            ->relationship('lotDestination', 'numero_lot')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->disabled(fn ($get) => ! $get('date_sevrage'))
                            ->helperText(fn ($get) => ! $get('date_sevrage')
                                ? '⚠️ Vous devez d\'abord saisir la date de sevrage'
                                : 'Lot où les porcelets seront transférés après sevrage'
                            )
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Lot de post-sevrage pour cette portée')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columnSpan(1),

                Section::make('Notes')
                    ->description('Observations complémentaires')
                    ->icon(Heroicon::DocumentText)
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Observations sur la mise bas, problèmes rencontrés, interventions...')
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
