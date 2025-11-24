<?php

namespace App\Filament\Resources\MouvementAliments\Schemas;

use App\Models\Aliment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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
                            ->afterStateUpdated(function (Get $get, $set, $state) {
                                $typeStock = match ($state) {
                                    'achat' => 'entrepot',
                                    'transfert_entree' => 'preparation',
                                    'transfert_sortie' => 'entrepot',
                                    'sortie_preparation' => 'preparation',
                                    default => null,
                                };
                                $set('type_stock', $typeStock);
                            })
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Type de mouvement d\'aliment à enregistrer')
                                    ->color('gray'),
                            ])),

                        Hidden::make('type_stock')
                            ->default(function (Get $get) {
                                return match ($get('type_mouvement')) {
                                    'achat' => 'entrepot',
                                    'transfert_entree' => 'preparation',
                                    'transfert_sortie' => 'entrepot',
                                    'sortie_preparation' => 'preparation',
                                    default => null,
                                };
                            }),

                        Select::make('aliment_id')
                            ->label('Aliment')
                            ->required()
                            ->relationship(
                                name: 'aliment',
                                titleAttribute: 'nom',
                                modifyQueryUsing: function ($query, Get $get) {
                                    $typeMouvement = $get('type_mouvement');

                                    // Pour les achats, tous les aliments actifs sont disponibles
                                    if ($typeMouvement === 'achat') {
                                        return $query->where('actif', true)->orderBy('nom');
                                    }

                                    // Pour les transferts depuis l'entrepôt, filtrer sur stock entrepôt > 0
                                    if ($typeMouvement === 'transfert_entree') {
                                        return $query->where('actif', true)
                                            ->whereHas('stockEntrepot', function ($q) {
                                                $q->where('poids_kg_disponible', '>', 0);
                                            })
                                            ->orderBy('nom');
                                    }

                                    // Pour les transferts/sorties depuis la préparation, filtrer sur stock préparation > 0
                                    if (in_array($typeMouvement, ['transfert_sortie', 'sortie_preparation'])) {
                                        return $query->where('actif', true)
                                            ->whereHas('stockPreparation', function ($q) {
                                                $q->where('poids_kg_disponible', '>', 0);
                                            })
                                            ->orderBy('nom');
                                    }

                                    // Par défaut, tous les aliments actifs
                                    return $query->where('actif', true)->orderBy('nom');
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->getOptionLabelFromRecordUsing(function ($record, Get $get) {
                                $typeMouvement = $get('type_mouvement');

                                // Pour achat, pas besoin d'afficher le stock
                                if ($typeMouvement === 'achat') {
                                    return $record->nom;
                                }

                                // Pour transfert depuis entrepôt, afficher stock entrepôt
                                if ($typeMouvement === 'transfert_entree') {
                                    $stock = $record->stockEntrepot?->poids_kg_disponible ?? 0;

                                    return "{$record->nom} ({$stock} kg en entrepôt)";
                                }

                                // Pour transfert/sortie depuis préparation, afficher stock préparation
                                if (in_array($typeMouvement, ['transfert_sortie', 'sortie_preparation'])) {
                                    $stock = $record->stockPreparation?->poids_kg_disponible ?? 0;

                                    return "{$record->nom} ({$stock} kg en préparation)";
                                }

                                return $record->nom;
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Seuls les aliments ayant un stock disponible dans la zone source sont affichés')
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
                            ->afterStateUpdated(function (Get $get, $set, $state) {
                                $prixUnitaire = $get('prix_unitaire_sac');

                                if ($state && $prixUnitaire && $prixUnitaire > 0) {
                                    $coutTotal = $state * $prixUnitaire;
                                    $set('cout_total', round($coutTotal, 2));
                                }
                            })
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
                            ->live(onBlur: true)
                            ->helperText(function (Get $get) {
                                $typeMouvement = $get('type_mouvement');
                                $alimentId = $get('aliment_id');

                                if (! $alimentId || $typeMouvement === 'achat') {
                                    return null;
                                }

                                $aliment = Aliment::with(['stockEntrepot', 'stockPreparation'])->find($alimentId);

                                if ($typeMouvement === 'transfert_entree') {
                                    $stockDispo = $aliment?->stockEntrepot?->poids_kg_disponible ?? 0;

                                    return "Stock entrepôt disponible : {$stockDispo} kg";
                                }

                                if (in_array($typeMouvement, ['transfert_sortie', 'sortie_preparation'])) {
                                    $stockDispo = $aliment?->stockPreparation?->poids_kg_disponible ?? 0;

                                    return "Stock préparation disponible : {$stockDispo} kg";
                                }

                                return null;
                            })
                            ->rules([
                                function (Get $get) {
                                    return function (string $attribute, $value, $fail) use ($get) {
                                        $typeMouvement = $get('type_mouvement');
                                        $alimentId = $get('aliment_id');

                                        // Pas de validation pour les achats
                                        if ($typeMouvement === 'achat' || ! $alimentId) {
                                            return;
                                        }

                                        $aliment = Aliment::with(['stockEntrepot', 'stockPreparation'])->find($alimentId);

                                        // Validation pour transfert depuis entrepôt
                                        if ($typeMouvement === 'transfert_entree') {
                                            $stockDispo = $aliment?->stockEntrepot?->poids_kg_disponible ?? 0;
                                            if ($value > $stockDispo) {
                                                $fail("La quantité demandée ({$value} kg) dépasse le stock entrepôt disponible ({$stockDispo} kg).");
                                            }
                                        }

                                        // Validation pour transfert/sortie depuis préparation
                                        if (in_array($typeMouvement, ['transfert_sortie', 'sortie_preparation'])) {
                                            $stockDispo = $aliment?->stockPreparation?->poids_kg_disponible ?? 0;
                                            if ($value > $stockDispo) {
                                                $fail("La quantité demandée ({$value} kg) dépasse le stock préparation disponible ({$stockDispo} kg).");
                                            }
                                        }
                                    };
                                },
                            ])
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
                            ->afterStateUpdated(function (Get $get, $set, $state) {
                                $nombreSacs = $get('nombre_sacs');

                                if ($state && $nombreSacs && $nombreSacs > 0) {
                                    $coutTotal = $state * $nombreSacs;
                                    $set('cout_total', round($coutTotal, 2));
                                }
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Prix d\'achat par sac. Le coût total sera calculé automatiquement.')
                                    ->color('gray'),
                            ])),

                        TextInput::make('cout_total')
                            ->label('Coût total')
                            ->numeric()
                            ->suffix('FCFA')
                            ->minValue(0)
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, $set, $state) {
                                $nombreSacs = $get('nombre_sacs');

                                if ($state && $nombreSacs && $nombreSacs > 0) {
                                    $prixUnitaire = $state / $nombreSacs;
                                    $set('prix_unitaire_sac', round($prixUnitaire, 2));
                                }
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Coût total de l\'achat. Vous pouvez le saisir directement ou il sera calculé depuis le prix unitaire.')
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
