<?php

namespace App\Filament\Resources\Animals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AnimalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'md' => 2,
            ])
            ->components([
                Section::make('Informations générales')
                    ->description('Identification et caractéristiques de l\'animal')
                    ->icon(Heroicon::Identification)
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ])
                    ->schema([
                        TextInput::make('numero_identification')
                            ->label('Numéro d\'identification')
                            ->placeholder('Ex: FR123456789')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            // ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro unique d\'identification de l\'animal')
                                    ->color('gray'),
                            ])),

                        ToggleButtons::make('type_animal')
                            ->label('Type d\'animal')
                            ->required()
                            ->options([
                                'truie' => 'Truie',
                                'verrat' => 'Verrat',
                            ])
                            ->icons([
                                'truie' => Heroicon::UserCircle,
                                'verrat' => Heroicon::UserCircle,
                            ])
                            ->live()
                            ->inline()
                           // ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Truie = femelle reproductrice. Verrat = mâle reproducteur')
                                    ->color('gray'),
                            ])),

                        Select::make('race_id')
                            ->label('Race')
                            ->relationship('race', 'nom')
                            ->native(false)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Race génétique de l\'animal')
                                    ->color('gray'),
                            ])),

                        Toggle::make('is_actif')
                            ->label('Statut actuel')
                            ->onIcon(Heroicon::Bolt)
                            ->offIcon(Heroicon::User)
                            ->default(true)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Statut actuel de l\'animal dans l\'élevage')
                                    ->color('gray'),
                            ])),

                        //                        Select::make('statut_actuel')
                        //                            ->label('Statut actuel')
                        //                            ->required()
                        //                            ->options([
                        //                                'active' => 'Active',
                        //                                'sevree' => 'Sevrée',
                        //                                'gestante' => 'Gestante',
                        //                                'en_lactation' => 'En lactation',
                        //                                'reforme' => 'Réformée',
                        //                            ])
                        //                            ->native(false)
                        //                            ->default('active')
                        //                            ->afterLabel(Schema::start([
                        //                                Icon::make(Heroicon::QuestionMarkCircle)
                        //                                    ->tooltip('Statut actuel de l\'animal dans l\'élevage')
                        //                                    ->color('gray'),
                        //                            ])),
                    ]),
                // ->columns(2)
                // ->columnSpan(2),

                Section::make('Origine et généalogie')
                    ->description('Provenance et parents de l\'animal')
                    ->icon(Heroicon::MapPin)
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ])
                    ->schema([
                        DatePicker::make('date_naissance')
                            ->label('Date de naissance')
                            ->native(false)
                            ->maxDate(now())
                            ->live()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date de naissance de l\'animal')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_entree')
                            ->label('Date d\'entrée')
                            ->required()
                            ->native(false)
                            ->minDate(fn (Get $get): ?string => $get('date_naissance'))
                            ->maxDate(now())
                            ->afterOrEqual('date_naissance')
                            ->helperText('La date d\'entrée doit être postérieure ou égale à la date de naissance')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date d\'entrée de l\'animal dans l\'élevage')
                                    ->color('gray'),
                            ])),
                        Select::make('origine')
                            ->label('Origine')
                            ->options([
                                'naissance_elevage' => 'Naissance dans l\'élevage',
                                'achat_externe' => 'Achat externe',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Provenance de l\'animal')
                                    ->color('gray'),
                            ])),

                        TextInput::make('provenance')
                            ->label('Provenance externe')
                            ->placeholder('Ex: Élevage Dupont, Fournisseur XYZ')
                            ->maxLength(50)
                            ->visible(fn (Get $get): bool => $get('origine') === 'achat_externe')
                            ->required(fn (Get $get): bool => $get('origine') === 'achat_externe')
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nom de l\'élevage ou du fournisseur')
                                    ->color('gray'),
                            ])),

                        TextInput::make('numero_mere')
                            ->label('Numéro de la mère')
                            ->placeholder('Ex: FR987654321')
                            ->maxLength(50)
                            ->visible(fn (Get $get): bool => $get('origine') === 'naissance_elevage')
                            ->required(fn (Get $get): bool => $get('origine') === 'naissance_elevage')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro d\'identification de la mère')
                                    ->color('gray'),
                            ])),

                        TextInput::make('numero_pere')
                            ->label('Numéro du père')
                            ->placeholder('Ex: FR123789456')
                            ->maxLength(50)
                            ->visible(fn (Get $get): bool => $get('origine') === 'naissance_elevage')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro d\'identification du père')
                                    ->color('gray'),
                            ])),
                    ]),
                // ->columnSpan(2),

                Section::make('Réforme')
                    ->description('Informations sur la sortie de l\'animal')
                    ->icon(Heroicon::ArchiveBox)
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ])
                    ->schema([
                        DatePicker::make('date_reforme')
                            ->label('Date de réforme')
                            ->native(false)
                            ->maxDate(now())
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date à laquelle l\'animal a été réformé')
                                    ->color('gray'),
                            ])),

                        Textarea::make('motif_reforme')
                            ->label('Motif de réforme')
                            ->placeholder('Ex: Âge avancé, problèmes de reproduction, santé...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Raison de la réforme')
                                    ->color('gray'),
                            ])),
                    ])
                    ->collapsed(),

                Section::make('Notes additionnelles')
                    ->description('Observations et informations complémentaires')
                    ->icon(Heroicon::DocumentText)
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Observations, comportements particuliers, informations importantes...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                  //  ->columnSpan(2)
                    ->collapsed(),
            ]);
    }
}
