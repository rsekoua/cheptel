<?php

namespace App\Filament\Resources\Lots\Actions;

use App\Filament\Resources\Lots\LotResource;
use App\Models\Lot;
use App\Models\Mouvement;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class TransfertEngraissementAction
{
    public static function make(): Action
    {
        return Action::make('transfert_engraissement')
            ->label('Transfert vers engraissement')
            ->icon(Heroicon::OutlinedArrowRight)
            ->color('warning')
            ->visible(fn ($record) => $record->type_lot === 'post_sevrage'
                && $record->statut_lot === 'actif'
                && $record->nb_animaux_actuel > 0)
            ->requiresConfirmation()
            ->modalHeading('Transfert vers l\'engraissement')
            ->modalDescription('Créer un nouveau lot d\'engraissement et clôturer le lot post-sevrage')
            ->schema([
                DateTimePicker::make('date_transfert')
                    ->label('Date et heure du transfert')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->default(now())
                    ->maxDate(now()),

                TextInput::make('numero_lot_engraissement')
                    ->label('Numéro du nouveau lot d\'engraissement')
                    ->required()
                    ->maxLength(50)
                    ->unique(table: 'lots', column: 'numero_lot')
                    ->placeholder('ENG-2025-001')
                    ->helperText('Numéro unique pour identifier le lot d\'engraissement'),

                TextInput::make('nb_animaux_transferes')
                    ->label('Nombre d\'animaux transférés')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('animaux')
                    ->default(fn ($record) => $record->nb_animaux_actuel)
                    ->rules([
                        fn ($record) => function ($attribute, $value, $fail) use ($record) {
                            if ($value > $record->nb_animaux_actuel) {
                                $fail("Le nombre d'animaux ne peut pas dépasser l'effectif actuel ({$record->nb_animaux_actuel}).");
                            }
                        },
                    ])
                    ->helperText('Nombre d\'animaux à transférer vers l\'engraissement'),

                Select::make('salle_destination_id')
                    ->label('Salle de destination')
                    ->required()
                    ->options(function () {
                        return \App\Models\Salle::whereHas('typeSalle', function ($query) {
                            $query->where('categorie', 'engraissement');
                        })
                            ->orWhereHas('typeSalle', function ($query) {
                                $query->where('nom', 'LIKE', '%engrais%');
                            })
                            ->pluck('nom', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->helperText('Salle d\'engraissement où sera placé le nouveau lot'),

                Select::make('plan_alimentation_id')
                    ->label('Plan d\'alimentation engraissement')
                    ->required()
                    ->options(function () {
                        return \App\Models\PlanAlimentation::where('type', 'production')
                            ->orWhere('nom', 'LIKE', '%engrais%')
                            ->pluck('nom', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->helperText('Plan alimentaire adapté à la phase d\'engraissement'),

                Textarea::make('notes')
                    ->label('Notes sur le transfert')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Observations sur l\'état des animaux, conditions du transfert...'),
            ])
            ->action(function ($record, array $data) {
                // Calculer les poids pour le nouveau lot
                $poidsMoyen = $record->poids_moyen_actuel_kg ?? 0;
                $poidsTotal = $poidsMoyen * $data['nb_animaux_transferes'];

                // Créer le nouveau lot d'engraissement
                $lotEngraissement = Lot::create([
                    'numero_lot' => $data['numero_lot_engraissement'],
                    'type_lot' => 'engraissement',
                    'date_creation' => $data['date_transfert'],
                    'nb_animaux_depart' => $data['nb_animaux_transferes'],
                    'nb_animaux_actuel' => $data['nb_animaux_transferes'],
                    'poids_total_depart_kg' => $poidsTotal,
                    'poids_moyen_depart_kg' => $poidsMoyen,
                    'poids_total_actuel_kg' => $poidsTotal,
                    'poids_moyen_actuel_kg' => $poidsMoyen,
                    'date_derniere_pesee' => $record->date_derniere_pesee,
                    'salle_id' => $data['salle_destination_id'],
                    'plan_alimentation_id' => $data['plan_alimentation_id'],
                    'statut_lot' => 'actif',
                    'notes' => "Lot créé par transfert depuis le lot {$record->numero_lot} (post-sevrage)."
                        .($data['notes'] ? "\n\n".$data['notes'] : ''),
                ]);

                // Transférer les portées liées au nouveau lot
                $portees = $record->portees;
                foreach ($portees as $portee) {
                    $lotEngraissement->portees()->attach($portee->id, [
                        'nb_porcelets_transferes' => $portee->pivot->nb_porcelets_transferes ?? $portee->nb_sevres,
                        'poids_total_transfere_kg' => $portee->pivot->poids_total_transfere_kg,
                    ]);
                }

                // Créer un mouvement pour la traçabilité
                Mouvement::create([
                    'lot_id' => $lotEngraissement->id,
                    'animal_id' => null,
                    'date_mouvement' => $data['date_transfert'],
                    'type_mouvement' => 'transfert',
                    'salle_origine_id' => $record->salle_id,
                    'salle_destination_id' => $data['salle_destination_id'],
                    'nb_animaux' => $data['nb_animaux_transferes'],
                    'motif' => "Transfert depuis lot post-sevrage {$record->numero_lot} vers engraissement",
                ]);

                // Clôturer le lot post-sevrage
                $record->update([
                    'statut_lot' => 'transfere',
                    'date_sortie' => $data['date_transfert'],
                    'nb_animaux_sortie' => $data['nb_animaux_transferes'],
                    'poids_total_sortie_kg' => $poidsTotal,
                    'poids_moyen_sortie_kg' => $poidsMoyen,
                    'destination_sortie' => "Lot engraissement {$data['numero_lot_engraissement']}",
                    'nb_animaux_actuel' => 0,
                ]);

                // Stocker l'ID du nouveau lot pour la redirection
                session()->put('nouveau_lot_id', $lotEngraissement->id);

                Notification::make()
                    ->success()
                    ->title('Transfert effectué avec succès')
                    ->body("{$data['nb_animaux_transferes']} animaux transférés vers le lot d'engraissement {$data['numero_lot_engraissement']}")
                    ->actions([
                        Action::make('voir_lot')
                            ->label('Voir le nouveau lot')
                            ->url(fn () => LotResource::getUrl('view', ['record' => session()->get('nouveau_lot_id')]))
                            ->button(),
                    ])
                    ->persistent()
                    ->send();
            })
            ->successRedirectUrl(function () {
                // Rediriger vers le nouveau lot créé
                $lotId = session()->pull('nouveau_lot_id');
                if ($lotId) {
                    return LotResource::getUrl('view', ['record' => $lotId]);
                }

                return null;
            });
    }
}
