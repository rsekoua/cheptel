<?php

namespace App\Filament\Resources\Lots\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class LotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->description('Informations de base sur le lot')
                    ->schema([
                        TextInput::make('numero_lot')
                            ->label('Numéro du lot')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro unique d\'identification du lot (ex: LOT-2025-001)')
                                    ->color('gray'),
                            ])),

                        Select::make('type_lot')
                            ->label('Type de lot')
                            ->required()
                            ->options([
                                'post_sevrage' => 'Post-sevrage',
                                'engraissement' => 'Engraissement',
                            ])
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Type de lot : Post-sevrage (porcelets sevrés) ou Engraissement (croissance jusqu\'à l\'abattage)')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_creation')
                            ->label('Date de création')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de constitution du lot')
                                    ->color('gray'),
                            ])),

                        Select::make('statut_lot')
                            ->label('Statut du lot')
                            ->required()
                            ->options([
                                'actif' => 'Actif',
                                'transfere' => 'Transféré',
                                'vendu' => 'Vendu',
                                'cloture' => 'Clôturé',
                            ])
                            ->default('actif')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Statut actuel du lot dans l\'élevage')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Effectif et poids au départ')
                    ->description('Données initiales lors de la constitution du lot')
                    ->schema([
                        TextInput::make('nb_animaux_depart')
                            ->label('Nombre d\'animaux au départ')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('animaux')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre d\'animaux lors de la constitution du lot')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_total_depart_kg')
                            ->label('Poids total au départ')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids total cumulé de tous les animaux au départ (en kilogrammes)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_moyen_depart_kg')
                            ->label('Poids moyen au départ')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids moyen par animal au départ (en kilogrammes)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(3),

                Section::make('Effectif et poids actuels')
                    ->description('Données actuelles du lot')
                    ->schema([
                        TextInput::make('nb_animaux_actuel')
                            ->label('Nombre d\'animaux actuel')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('animaux')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre d\'animaux actuellement dans le lot (peut diminuer en cas de mortalité ou vente)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_total_actuel_kg')
                            ->label('Poids total actuel')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids total cumulé actuel de tous les animaux du lot')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_moyen_actuel_kg')
                            ->label('Poids moyen actuel')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids moyen actuel par animal dans le lot')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_derniere_pesee')
                            ->label('Date de dernière pesée')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de la dernière pesée collective du lot')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Localisation et alimentation')
                    ->description('Emplacement et plan alimentaire du lot')
                    ->schema([
                        Select::make('salle_id')
                            ->label('Salle')
                            ->relationship('salle', 'nom')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Salle/bâtiment où se trouve actuellement le lot')
                                    ->color('gray'),
                            ])),

                        Select::make('plan_alimentation_id')
                            ->label('Plan d\'alimentation')
                            ->relationship('planAlimentation', 'nom')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Plan alimentaire appliqué au lot selon le stade de croissance')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Sortie du lot')
                    ->description('Informations sur la sortie/vente du lot')
                    ->schema([
                        DatePicker::make('date_sortie')
                            ->label('Date de sortie')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de sortie du lot (vente, abattage, transfert)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('nb_animaux_sortie')
                            ->label('Nombre d\'animaux sortis')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('animaux')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre d\'animaux concernés par la sortie')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_total_sortie_kg')
                            ->label('Poids total à la sortie')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids total cumulé des animaux sortis')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_moyen_sortie_kg')
                            ->label('Poids moyen à la sortie')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids moyen par animal à la sortie')
                                    ->color('gray'),
                            ])),

                        TextInput::make('prix_vente_total')
                            ->label('Prix de vente total')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Montant total de la vente du lot')
                                    ->color('gray'),
                            ])),

                        TextInput::make('destination_sortie')
                            ->label('Destination de sortie')
                            ->maxLength(100)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Destination des animaux (abattoir, autre élevage, etc.)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2)
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
                                    ->tooltip('Observations ou informations complémentaires sur le lot')
                                    ->color('gray'),
                            ])),
                    ])
                    ->collapsed(),
            ]);
    }
}
