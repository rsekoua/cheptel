<?php

namespace App\Filament\Resources\MouvementAliments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MouvementAlimentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Type de mouvement')
                    ->description(fn (Get $get) => match ($get('type_mouvement')) {
                        'achat' => 'Enregistrement d\'un achat d\'aliment vers l\'entrepôt',
                        'transfert_entree' => 'Transfert d\'aliment de l\'entrepôt vers la zone de préparation',
                        'transfert_sortie' => 'Retour d\'aliment de la zone de préparation vers l\'entrepôt',
                        'sortie_preparation' => 'Sortie d\'aliment de la préparation pour utilisation',
                        default => 'Sélectionnez le type de mouvement à effectuer',
                    })
                    ->columns(2)
                    ->schema([
                        Select::make('type_mouvement')
                            ->label('Type de mouvement')
                            ->required()
                            ->options([
                                'achat' => 'Achat (vers entrepôt)',
                                'transfert_entree' => 'Transfert vers préparation',
                                'transfert_sortie' => 'Retour vers entrepôt',
                                'sortie_preparation' => 'Sortie pour utilisation',
                            ])
                            ->native(false)
                            ->live()
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Type de mouvement d\'aliment à enregistrer')
                                    ->color('gray'),
                            ])),

                        Select::make('aliment_id')
                            ->label('Aliment')
                            ->required()
                            ->relationship('aliment', 'nom')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Sélectionner l\'aliment concerné par ce mouvement')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_mouvement')
                            ->label('Date du mouvement')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->maxDate(now())
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date à laquelle le mouvement a été effectué')
                                    ->color('gray'),
                            ])),
                    ]),

                Section::make('Quantité et poids')
                    ->description('Informations sur la quantité d\'aliment concernée par ce mouvement')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre_sacs')
                            ->label('Nombre de sacs')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('sacs')
                            ->visible(fn (Get $get) => $get('type_mouvement') === 'achat')
                            ->live(onBlur: true)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre de sacs achetés')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_kg')
                            ->label('Poids total')
                            ->required()
                            ->numeric()
                            ->suffix('kg')
                            ->minValue(0.01)
                            ->step(0.01)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids total de l\'aliment en kilogrammes')
                                    ->color('gray'),
                            ])),
                    ]),

                Section::make('Informations financières')
                    ->description('Coûts liés à l\'achat de l\'aliment')
                    ->columns(3)
                    ->visible(fn (Get $get) => $get('type_mouvement') === 'achat')
                    ->schema([
                        TextInput::make('prix_unitaire_sac')
                            ->label('Prix unitaire')
                            ->numeric()
                            ->suffix('FCFA')
                            ->minValue(0)
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Prix d\'achat par sac')
                                    ->color('gray'),
                            ])),

                        TextInput::make('cout_total')
                            ->label('Coût total')
                            ->numeric()
                            ->suffix('FCFA')
                            ->minValue(0)
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, $set) {
                                $coutTotal = $get('cout_total');
                                $nombreSacs = $get('nombre_sacs');

                                if ($coutTotal && $nombreSacs && $nombreSacs > 0) {
                                    $prixUnitaire = $coutTotal / $nombreSacs;
                                    $set('prix_unitaire_sac', round($prixUnitaire, 2));
                                }
                            })
                            ->live(onBlur: true)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Coût total de l\'achat (sera calculé automatiquement si vous renseignez le prix unitaire et le nombre de sacs)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('reference')
                            ->label('Référence')
                            ->maxLength(255)
                            ->placeholder('N° facture, bon de commande...')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Numéro de facture, bon de commande, ou autre référence')
                                    ->color('gray'),
                            ])),
                    ]),

                Section::make('Informations complémentaires')
                    ->description('Notes et informations additionnelles')
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Ajoutez des notes ou informations complémentaires...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
