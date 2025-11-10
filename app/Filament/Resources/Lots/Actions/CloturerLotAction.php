<?php

namespace App\Filament\Resources\Lots\Actions;

use App\Models\Mouvement;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class CloturerLotAction
{
    public static function make(): Action
    {
        return Action::make('cloturer_lot')
            ->label('Clôturer le lot')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->visible(fn ($record) => $record->statut_lot === 'actif')
            ->requiresConfirmation()
            ->modalHeading('Clôturer le lot')
            ->modalDescription('Finaliser le lot avec les informations de sortie (vente, abattage, réforme)')
            ->schema([
                DateTimePicker::make('date_sortie')
                    ->label('Date et heure de sortie')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->default(now())
                    ->maxDate(now()),

                Select::make('type_sortie')
                    ->label('Type de sortie')
                    ->required()
                    ->options([
                        'vente' => 'Vente (abattoir)',
                        'vente_vif' => 'Vente en vif',
                        'reforme' => 'Réforme',
                        'autre' => 'Autre',
                    ])
                    ->default('vente')
                    ->native(false)
                    ->live(),

                TextInput::make('nb_animaux_sortie')
                    ->label('Nombre d\'animaux sortis')
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
                    ]),

                TextInput::make('poids_total_sortie_kg')
                    ->label('Poids total à la sortie')
                    ->required()
                    ->numeric()
                    ->suffix('kg')
                    ->step(0.01)
                    ->minValue(0.01)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $nbAnimaux = $get('nb_animaux_sortie');
                        if ($nbAnimaux && $nbAnimaux > 0 && $state) {
                            $poidsMoyen = $state / $nbAnimaux;
                            $set('poids_moyen_sortie_kg', round($poidsMoyen, 2));
                        }
                    })
                    ->default(fn ($record) => $record->poids_total_actuel_kg),

                TextInput::make('poids_moyen_sortie_kg')
                    ->label('Poids moyen à la sortie')
                    ->numeric()
                    ->suffix('kg')
                    ->step(0.01)
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Calculé automatiquement'),

                TextInput::make('prix_vente_total')
                    ->label('Prix de vente total')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01)
                    ->minValue(0)
                    ->visible(fn ($get) => in_array($get('type_sortie'), ['vente', 'vente_vif']))
                    ->helperText('Montant total de la vente (optionnel)'),

                TextInput::make('destination_sortie')
                    ->label('Destination')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Nom de l\'abattoir, acheteur, etc.')
                    ->helperText('Destination finale des animaux'),

                Textarea::make('notes')
                    ->label('Notes et observations')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Informations complémentaires sur la sortie...'),
            ])
            ->action(function ($record, array $data) {
                // Calculer les performances finales
                $gmq = $record->gmq;
                $tauxMortalite = $record->taux_mortalite;
                $joursElevage = $record->date_creation->diffInDays($data['date_sortie']);

                // Créer un mouvement de sortie pour la traçabilité
                Mouvement::create([
                    'lot_id' => $record->id,
                    'animal_id' => null,
                    'date_mouvement' => $data['date_sortie'],
                    'type_mouvement' => 'sortie',
                    'salle_origine_id' => $record->salle_id,
                    'salle_destination_id' => null,
                    'nb_animaux' => $data['nb_animaux_sortie'],
                    'motif' => "Clôture du lot - {$data['type_sortie']} - Destination : {$data['destination_sortie']}",
                ]);

                // Mettre à jour le lot avec les informations de sortie
                $record->update([
                    'statut_lot' => in_array($data['type_sortie'], ['vente', 'vente_vif']) ? 'vendu' : 'cloture',
                    'date_sortie' => $data['date_sortie'],
                    'nb_animaux_sortie' => $data['nb_animaux_sortie'],
                    'poids_total_sortie_kg' => $data['poids_total_sortie_kg'],
                    'poids_moyen_sortie_kg' => $data['poids_moyen_sortie_kg'],
                    'prix_vente_total' => $data['prix_vente_total'] ?? null,
                    'destination_sortie' => $data['destination_sortie'],
                    'nb_animaux_actuel' => $record->nb_animaux_actuel - $data['nb_animaux_sortie'],
                    'notes' => $record->notes
                        .($record->notes ? "\n\n" : '')
                        ."=== CLÔTURE DU LOT ===\n"
                        ."Date : ".now()->format('d/m/Y H:i')."\n"
                        ."Type : {$data['type_sortie']}\n"
                        ."Destination : {$data['destination_sortie']}\n"
                        ."Durée d'élevage : {$joursElevage} jours\n"
                        ."GMQ final : ".($gmq ? "{$gmq} g/j" : 'N/A')."\n"
                        ."Taux de mortalité : ".($tauxMortalite ? number_format($tauxMortalite, 2).' %' : 'N/A')."\n"
                        .($data['notes'] ? "\nNotes : {$data['notes']}" : ''),
                ]);

                // Construire le message de notification avec les performances
                $body = "{$data['nb_animaux_sortie']} animaux clôturés.";
                $body .= "\nPoids moyen final : {$data['poids_moyen_sortie_kg']} kg";

                if ($gmq) {
                    $body .= "\nGMQ : {$gmq} g/jour";
                }

                if ($tauxMortalite !== null) {
                    $body .= "\nTaux de mortalité : ".number_format($tauxMortalite, 2).' %';
                }

                if (isset($data['prix_vente_total']) && $data['prix_vente_total'] > 0) {
                    $body .= "\nPrix de vente : ".number_format($data['prix_vente_total'], 2).' €';
                    $prixParAnimal = $data['prix_vente_total'] / $data['nb_animaux_sortie'];
                    $body .= "\nPrix moyen par animal : ".number_format($prixParAnimal, 2).' €';
                }

                Notification::make()
                    ->success()
                    ->title('Lot clôturé avec succès')
                    ->body($body)
                    ->persistent()
                    ->send();
            })
            ->after(fn () => redirect()->back());
    }
}
