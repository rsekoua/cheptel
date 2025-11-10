<?php

namespace App\Filament\Resources\Portees\Actions;

use App\Models\Lot;
use App\Models\Portee;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SevrerPorteesAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('sevrer')
            ->label('Sevrer les portées')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Sevrage groupé de portées')
            ->modalDescription('Cette action va sevrer toutes les portées sélectionnées et créer ou alimenter un lot de post-sevrage.')
            ->modalWidth('4xl')
            ->schema([
                Grid::make(2)
                    ->schema([
                        DatePicker::make('date_sevrage')
                            ->label('Date de sevrage')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->maxDate(now())
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date du sevrage (généralement 21-28 jours après la mise-bas)')
                                    ->color('gray'),
                            ])),

                        Radio::make('mode_lot')
                            ->label('Destination des porcelets')
                            ->required()
                            ->options([
                                'existant' => 'Ajouter à un lot existant',
                                'nouveau' => 'Créer un nouveau lot',
                            ])
                            ->default('nouveau')
                            ->live()
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Choisissez si vous voulez créer un nouveau lot ou ajouter à un lot existant')
                                    ->color('gray'),
                            ])),
                    ]),

                // Option 1 : Lot existant
                Group::make([
                    Select::make('lot_existant_id')
                        ->label('Lot de destination')
                        ->options(
                            Lot::query()
                                ->where('statut_lot', 'actif')
                                ->where('type_lot', 'post_sevrage')
                                ->orderBy('date_creation', 'desc')
                                ->pluck('numero_lot', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->afterLabel(Schema::start([
                            Icon::make(Heroicon::QuestionMarkCircle)
                                ->tooltip('Lot de post-sevrage actif où transférer les porcelets')
                                ->color('gray'),
                        ])),
                ])
                    ->visible(fn ($get) => $get('mode_lot') === 'existant'),

                // Option 2 : Nouveau lot
                Group::make([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('nouveau_lot_numero')
                                ->label('Numéro du nouveau lot')
                                ->required()
                                ->unique(Lot::class, 'numero_lot')
                                ->placeholder('Ex: Lot-PS-S44-2024')
                                ->helperText('Format recommandé : Lot-PS-S[Semaine]-[Année]')
                                ->afterLabel(Schema::start([
                                    Icon::make(Heroicon::QuestionMarkCircle)
                                        ->tooltip('Numéro unique pour identifier ce lot de post-sevrage')
                                        ->color('gray'),
                                ])),

                            Select::make('salle_id')
                                ->label('Salle de destination')
                                ->relationship(
                                    'salle',
                                    'nom',
                                    fn ($query) => $query->whereHas('typeSalle', fn ($q) => $q->where('nom', 'Post-sevrage'))
                                        ->where('statut', 'disponible')
                                )
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->afterLabel(Schema::start([
                                    Icon::make(Heroicon::QuestionMarkCircle)
                                        ->tooltip('Salle de post-sevrage où sera placé le lot')
                                        ->color('gray'),
                                ])),

                            Select::make('plan_alimentation_id')
                                ->label('Plan d\'alimentation')
                                ->relationship(
                                    'planAlimentation',
                                    'nom',
                                    fn ($query) => $query->where('type_animal', 'production')
                                )
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->afterLabel(Schema::start([
                                    Icon::make(Heroicon::QuestionMarkCircle)
                                        ->tooltip('Plan d\'alimentation pour le post-sevrage')
                                        ->color('gray'),
                                ])),

                            Textarea::make('notes_lot')
                                ->label('Notes sur le lot')
                                ->rows(3)
                                ->placeholder('Observations sur le lot, origine, qualité, etc.')
                                ->afterLabel(Schema::start([
                                    Icon::make(Heroicon::QuestionMarkCircle)
                                        ->tooltip('Informations complémentaires sur ce lot')
                                        ->color('gray'),
                                ])),
                        ]),
                ])
                    ->visible(fn ($get) => $get('mode_lot') === 'nouveau'),

                // Données par portée
                Repeater::make('donnees_portees')
                    ->label('Données de sevrage par portée')
                    ->schema([
                        TextInput::make('portee_id')
                            ->label('Portée')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('truie')
                            ->label('Truie')
                            ->disabled(),

                        TextInput::make('nb_sevres')
                            ->label('Nombre de sevrés')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('porcelets')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $nbSevres = $state ?? 0;
                                $poidsTotal = $get('poids_total_kg') ?? 0;
                                $set('poids_moyen_kg', $nbSevres > 0 ? number_format($poidsTotal / $nbSevres, 2) : '0.00');

                                // Mettre à jour les totaux
                                static::updateTotals($set, $get);
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nombre réel de porcelets sevrés vivants (peut être inférieur aux nés vivants en cas de mortalité)')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_total_kg')
                            ->label('Poids total')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('kg')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $nbSevres = $get('nb_sevres') ?? 0;
                                $poidsTotal = $state ?? 0;
                                $set('poids_moyen_kg', $nbSevres > 0 ? number_format($poidsTotal / $nbSevres, 2) : '0.00');

                                // Mettre à jour les totaux
                                static::updateTotals($set, $get);
                            })
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Poids total de tous les porcelets de cette portée')
                                    ->color('gray'),
                            ])),

                        TextInput::make('poids_moyen_kg')
                            ->label('Poids moyen')
                            ->disabled()
                            ->suffix('kg')
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, $get) {
                                $nbSevres = $get('nb_sevres') ?? 0;
                                $poidsTotal = $get('poids_total_kg') ?? 0;
                                $component->state($nbSevres > 0 ? number_format($poidsTotal / $nbSevres, 2) : '0.00');
                            }),
                    ])
                    ->columns(5)
                    ->reorderable(false)
                    ->addable(false)
                    ->deletable(false)
                    ->defaultItems(0)
                    ->afterLabel(Schema::start([
                        Icon::make(Heroicon::QuestionMarkCircle)
                            ->tooltip('Saisissez les données de sevrage pour chaque portée sélectionnée')
                            ->color('gray'),
                    ]))
                    ->live(),

                // Totaux récapitulatifs
                Group::make([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('total_porcelets_calcule')
                                ->label('Total porcelets')
                                ->disabled()
                                ->dehydrated(false)
                                ->suffix('porcelets')
                                ->helperText('Ce nombre sera ajouté au lot'),

                            TextInput::make('total_poids_calcule')
                                ->label('Poids total')
                                ->disabled()
                                ->dehydrated(false)
                                ->suffix('kg')
                                ->helperText('Poids total qui sera ajouté au lot'),

                            TextInput::make('poids_moyen_global')
                                ->label('Poids moyen global')
                                ->disabled()
                                ->dehydrated(false)
                                ->suffix('kg')
                                ->helperText('Poids moyen par porcelet'),
                        ]),
                ])
                    ->visible(fn ($get) => ! empty($get('donnees_portees'))),
            ])
            ->fillForm(function (Collection $records) {
                $donneesPortees = $records->map(function (Portee $portee) {
                    return [
                        'portee_id' => $portee->id,
                        'truie' => $portee->animal->numero_identification,
                        'nb_sevres' => $portee->nb_nes_vifs ?? 0,
                        'poids_total_kg' => 0,
                        'poids_moyen_kg' => 0,
                    ];
                })->toArray();

                return [
                    'donnees_portees' => $donneesPortees,
                ];
            })
            ->action(function (Collection $records, array $data) {
                DB::transaction(function () use ($data) {
                    // 1. Créer ou récupérer le lot
                    $lot = static::getOrCreateLot($data);

                    // Variables pour calculer les totaux réels
                    // IMPORTANT: Ces totaux DOIVENT correspondre exactement à la somme
                    // des porcelets de toutes les portées sevrées
                    $totalPorcelets = 0;
                    $totalPoids = 0;

                    // 2. Traiter chaque portée
                    foreach ($data['donnees_portees'] as $donneePortee) {
                        $portee = Portee::find($donneePortee['portee_id']);

                        if (! $portee) {
                            continue;
                        }

                        // Vérifier que la portée n'est pas déjà sevrée
                        if ($portee->date_sevrage) {
                            Notification::make()
                                ->warning()
                                ->title('Portée déjà sevrée')
                                ->body("La portée de {$portee->animal->numero_identification} est déjà sevrée.")
                                ->send();

                            continue;
                        }

                        $nbSevres = $donneePortee['nb_sevres'];
                        $poidsTotal = $donneePortee['poids_total_kg'];
                        $poidsMoyen = $nbSevres > 0 ? $poidsTotal / $nbSevres : 0;

                        // Mettre à jour la portée
                        $portee->update([
                            'date_sevrage' => $data['date_sevrage'],
                            'nb_sevres' => $nbSevres,
                            'poids_total_sevrage_kg' => $poidsTotal,
                            'poids_moyen_sevrage_kg' => $poidsMoyen,
                            'lot_destination_id' => $lot->id,
                        ]);

                        // Enregistrer la relation dans la table pivot
                        $portee->lots()->syncWithoutDetaching([
                            $lot->id => [
                                'nb_porcelets_transferes' => $nbSevres,
                                'poids_total_transfere_kg' => $poidsTotal,
                            ],
                        ]);

                        // Mettre à jour le cycle de reproduction
                        $cycleReproduction = $portee->cycleReproduction;
                        if ($cycleReproduction && $cycleReproduction->statut_cycle === 'en_cours') {
                            $cycleReproduction->update([
                                'statut_cycle' => 'termine_succes',
                            ]);
                        }

                        // Mettre à jour le statut de la truie
                        $truie = $portee->animal;
                        if ($truie && $truie->statut_actuel === 'en_lactation') {
                            $truie->update([
                                'statut_actuel' => 'sevree',
                            ]);
                        }

                        // Accumuler les totaux réels
                        $totalPorcelets += $nbSevres;
                        $totalPoids += $poidsTotal;
                    }

                    // Vérification de cohérence : le nombre de portées traitées doit correspondre
                    if ($totalPorcelets === 0) {
                        Notification::make()
                            ->danger()
                            ->title('Aucun porcelet à sevrer')
                            ->body('Aucune portée valide n\'a pu être sevrée.')
                            ->send();

                        return;
                    }

                    // 3. Mettre à jour les données du lot
                    // Les totaux du lot seront EXACTEMENT égaux à la somme des portées
                    static::updateLot($lot, $totalPorcelets, $totalPoids, $data);
                });

                Notification::make()
                    ->success()
                    ->title('Sevrage effectué')
                    ->body("{$records->count()} portée(s) ont été sevrées avec succès.")
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    protected static function getOrCreateLot(array $data): Lot
    {
        if ($data['mode_lot'] === 'existant') {
            return Lot::findOrFail($data['lot_existant_id']);
        }

        // Créer un nouveau lot
        return Lot::create([
            'numero_lot' => $data['nouveau_lot_numero'],
            'type_lot' => 'post_sevrage',
            'date_creation' => $data['date_sevrage'],
            'nb_animaux_depart' => 0,
            'nb_animaux_actuel' => 0,
            'poids_total_depart_kg' => 0,
            'poids_moyen_depart_kg' => 0,
            'poids_total_actuel_kg' => 0,
            'poids_moyen_actuel_kg' => 0,
            'salle_id' => $data['salle_id'] ?? null,
            'statut_lot' => 'actif',
            'plan_alimentation_id' => $data['plan_alimentation_id'] ?? null,
            'notes' => $data['notes_lot'] ?? null,
        ]);
    }

    /**
     * Met à jour les effectifs et poids du lot
     *
     * RÈGLE FONDAMENTALE :
     * Le nombre de porcelets du lot = SOMME EXACTE des porcelets de toutes ses portées
     *
     * @param  Lot  $lot  Le lot à mettre à jour
     * @param  int  $totalPorcelets  Somme des nb_sevres de toutes les portées
     * @param  float  $totalPoids  Somme des poids_total_sevrage_kg de toutes les portées
     * @param  array  $data  Données du formulaire
     */
    protected static function updateLot(Lot $lot, int $totalPorcelets, float $totalPoids, array $data): void
    {
        // Calcul des nouveaux totaux si on ajoute à un lot existant
        $nouveauNbAnimaux = $lot->nb_animaux_actuel + $totalPorcelets;
        $nouveauPoidsTotal = $lot->poids_total_actuel_kg + $totalPoids;
        $nouveauPoidsMoyen = $nouveauNbAnimaux > 0 ? $nouveauPoidsTotal / $nouveauNbAnimaux : 0;

        // Si c'est le premier remplissage du lot (nouveau lot)
        if ($lot->nb_animaux_depart === 0) {
            // Les effectifs du lot = exactement la somme des portées
            $lot->update([
                'nb_animaux_depart' => $totalPorcelets,  // = Σ(nb_sevres)
                'nb_animaux_actuel' => $totalPorcelets,  // = Σ(nb_sevres)
                'poids_total_depart_kg' => $totalPoids, // = Σ(poids_total_sevrage_kg)
                'poids_moyen_depart_kg' => $totalPorcelets > 0 ? $totalPoids / $totalPorcelets : 0,
                'poids_total_actuel_kg' => $totalPoids, // = Σ(poids_total_sevrage_kg)
                'poids_moyen_actuel_kg' => $totalPorcelets > 0 ? $totalPoids / $totalPorcelets : 0,
                'date_derniere_pesee' => $data['date_sevrage'],
            ]);
        } else {
            // Ajout à un lot existant : on cumule les effectifs
            $lot->update([
                'nb_animaux_actuel' => $nouveauNbAnimaux,        // Anciens + Σ(nb_sevres)
                'poids_total_actuel_kg' => $nouveauPoidsTotal,   // Ancien poids + Σ(poids_total_sevrage_kg)
                'poids_moyen_actuel_kg' => $nouveauPoidsMoyen,   // Recalculé sur tous les animaux
                'date_derniere_pesee' => $data['date_sevrage'],
            ]);
        }
    }

    /**
     * Met à jour les totaux calculés (total porcelets, total poids, poids moyen global)
     */
    protected static function updateTotals($set, $get): void
    {
        $donneesPortees = $get('../../donnees_portees') ?? [];

        $totalPorcelets = collect($donneesPortees)->sum('nb_sevres');
        $totalPoids = collect($donneesPortees)->sum('poids_total_kg');
        $poidsMoyenGlobal = $totalPorcelets > 0 ? $totalPoids / $totalPorcelets : 0;

        $set('../../total_porcelets_calcule', $totalPorcelets);
        $set('../../total_poids_calcule', number_format($totalPoids, 2));
        $set('../../poids_moyen_global', number_format($poidsMoyenGlobal, 2));
    }
}
