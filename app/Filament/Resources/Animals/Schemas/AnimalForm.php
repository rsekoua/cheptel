<?php

namespace App\Filament\Resources\Animals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AnimalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification')
                    ->description('Informations d\'identification de l\'animal')
                    ->schema([
                        TextInput::make('numero_identification')
                            ->label('Numéro d\'identification')
                            ->required()
                            ->maxLength(50)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro unique d\'identification de l\'animal (ex: FR123456789)')
                                    ->color('gray'),
                            ])),

                        //                        Select::make('type_animal')
                        ToggleButtons::make('type_animal')
                            ->label('Type d\'animal')
                            ->required()
                            ->options([
                                'truie' => 'Truie',
                                'cochette' => 'Cochette',
                                'verrat' => 'Verrat',
                            ])
                           // ->native(false)
                            ->live()
                            ->inline()
                            ->afterStateUpdated(function ($state, $set) {
                                // Définir automatiquement le sexe en fonction du type
                                if (in_array($state, ['truie', 'cochette'])) {
                                    $set('sexe', 'F');
                                } elseif ($state === 'verrat') {
                                    $set('sexe', 'M');
                                }
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Truie = femelle reproductrice ayant déjà mis bas. Cochette = jeune femelle reproductrice (pas encore mis bas). Verrat = mâle reproducteur')
                                    ->color('gray'),
                            ])),

                        Select::make('race_id')
                            ->label('Race')
                            ->relationship('race', 'nom')
                            ->native(false)
                            ->required()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Race génétique de l\'animal')
                                    ->color('gray'),
                            ])),

                        Select::make('sexe')
                            ->label('Sexe')
                            ->required()
                            ->options([
                                'F' => 'Femelle',
                                'M' => 'Mâle',
                            ])
                            ->native(false)
                            ->disabled(fn ($get) => in_array($get('type_animal'), ['truie', 'cochette', 'verrat']))
                            ->dehydrated()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Sexe biologique de l\'animal (rempli automatiquement selon le type)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Origine et généalogie')
                    ->description('Informations sur la naissance et les parents')
                    ->schema([
                        Select::make('origine')
                            ->label('Origine')
                            ->options([
                                'naissance_elevage' => 'Naissance dans l\'élevage',
                                'achat_externe' => 'Achat externe',
                            ])
                            ->required()
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Provenance de l\'animal (naissance dans l\'élevage ou achat externe)')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_naissance')
                            ->label('Date de naissance')
                            ->required()
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de naissance de l\'animal')
                                    ->color('gray'),
                            ])),

                        TextInput::make('numero_mere')
                            ->label('Numéro de la mère')
                            ->maxLength(50)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro d\'identification de la mère (si connu)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('numero_pere')
                            ->label('Numéro du père')
                            ->maxLength(50)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro d\'identification du père (si connu)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Gestion d\'élevage')
                    ->description('Informations sur l\'entrée et la gestion')
                    ->schema([
                        DatePicker::make('date_entree')
                            ->label('Date d\'entrée')
                            ->required()
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date d\'entrée de l\'animal dans l\'élevage')
                                    ->color('gray'),
                            ])),

                        TextInput::make('bande')
                            ->label('Bande')
                            ->maxLength(50)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro ou identifiant de la bande/groupe de production')
                                    ->color('gray'),
                            ])),

                        Select::make('statut_actuel')
                            ->label('Statut actuel')
                            ->required()
                            ->options([
                                'sevree' => 'Sevrée',
                                'en_chaleurs' => 'En chaleurs',
                                'gestante_attente' => 'Gestante (en attente confirmation)',
                                'gestante_confirmee' => 'Gestante (confirmée)',
                                'en_lactation' => 'En lactation',
                                'reforme' => 'Réformée',
                                'active' => 'Active',
                                'retraite' => 'Retraite',
                            ])
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Statut actuel de l\'animal dans l\'élevage')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(3),

                Section::make('Localisation')
                    ->description('Emplacement actuel de l\'animal')
                    ->schema([
                        Select::make('salle_id')
                            ->label('Salle')
                            ->relationship('salle', 'nom')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Salle/bâtiment où se trouve actuellement l\'animal')
                                    ->color('gray'),
                            ])),

                        TextInput::make('place_numero')
                            ->label('Numéro de place')
                            ->maxLength(20)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro de la case ou de la place spécifique dans la salle')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Suivi pondéral')
                    ->description('Suivi du poids de l\'animal')
                    ->schema([
                        TextInput::make('poids_actuel_kg')
                            ->label('Poids actuel (kg)')
                            ->numeric()
                            ->suffix('kg')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Dernier poids enregistré de l\'animal en kilogrammes')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_derniere_pesee')
                            ->label('Date de dernière pesée')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de la dernière pesée de l\'animal')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2),

                Section::make('Alimentation')
                    ->description('Plan alimentaire de l\'animal')
                    ->schema([
                        Select::make('plan_alimentation_id')
                            ->label('Plan d\'alimentation')
                            ->relationship('planAlimentation', 'nom')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Plan d\'alimentation assigné à l\'animal selon son stade physiologique')
                                    ->color('gray'),
                            ])),
                    ]),

                Section::make('Réforme')
                    ->description('Informations sur la réforme de l\'animal')
                    ->schema([
                        DatePicker::make('date_reforme')
                            ->label('Date de réforme')
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date à laquelle l\'animal a été réformé (retiré de la reproduction)')
                                    ->color('gray'),
                            ])),

                        Textarea::make('motif_reforme')
                            ->label('Motif de réforme')
                            ->rows(3)
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Raison de la réforme (ex: âge, problèmes de reproduction, santé, performances insuffisantes)')
                                    ->color('gray'),
                            ])),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Section::make('Notes additionnelles')
                    ->description('Informations complémentaires')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Observations ou informations complémentaires sur l\'animal')
                                    ->color('gray'),
                            ])),
                    ])
                    ->collapsed(),
            ]);
    }
}
