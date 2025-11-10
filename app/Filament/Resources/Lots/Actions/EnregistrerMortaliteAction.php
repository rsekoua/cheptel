<?php

namespace App\Filament\Resources\Lots\Actions;

use App\Models\EvenementsSanitaire;
use App\Models\Tache;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class EnregistrerMortaliteAction
{
    public static function make(): Action
    {
        return Action::make('enregistrer_mortalite')
            ->label('Enregistrer une mortalité')
            ->icon(Heroicon::OutlinedExclamationTriangle)
            ->color('danger')
            ->visible(fn ($record) => $record->statut_lot === 'actif' && $record->nb_animaux_actuel > 0)
            ->requiresConfirmation()
            ->modalDescription('Enregistrer la mortalité d\'un ou plusieurs animaux du lot')
            ->schema([
                DateTimePicker::make('date_heure')
                    ->label('Date et heure de la mortalité')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->default(now())
                    ->maxDate(now()),

                TextInput::make('nb_morts')
                    ->label('Nombre d\'animaux morts')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('animaux')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $record) {
                        $nouveauEffectif = $record->nb_animaux_actuel - $state;
                        $set('nouvel_effectif', max(0, $nouveauEffectif));
                    })
                    ->rules([
                        fn ($record) => function ($attribute, $value, $fail) use ($record) {
                            if ($value > $record->nb_animaux_actuel) {
                                $fail("Le nombre de morts ne peut pas dépasser l'effectif actuel ({$record->nb_animaux_actuel} animaux).");
                            }
                        },
                    ]),

                TextInput::make('nouvel_effectif')
                    ->label('Nouvel effectif après mortalité')
                    ->numeric()
                    ->suffix('animaux')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Calculé automatiquement'),

                Select::make('cause')
                    ->label('Cause de la mortalité')
                    ->options([
                        'maladie' => 'Maladie',
                        'accident' => 'Accident',
                        'faiblesse' => 'Faiblesse/Retard de croissance',
                        'cannibalisme' => 'Cannibalisme',
                        'stress' => 'Stress',
                        'inconnue' => 'Inconnue',
                        'autre' => 'Autre',
                    ])
                    ->native(false)
                    ->live()
                    ->helperText('Cause principale de la mortalité'),

                Textarea::make('notes')
                    ->label('Notes et observations')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Observations, symptômes, circonstances...'),

                Select::make('creer_tache')
                    ->label('Créer une tâche de suivi ?')
                    ->options([
                        'non' => 'Non',
                        'oui' => 'Oui (mortalité anormale)',
                    ])
                    ->default(fn ($get) => $get('nb_morts') > 2 ? 'oui' : 'non')
                    ->native(false)
                    ->live()
                    ->visible(fn ($get) => $get('nb_morts') > 1)
                    ->helperText('Recommandé si la mortalité est élevée (> 2 animaux) ou suspecte'),
            ])
            ->action(function ($record, array $data) {
                // Décrémenter l'effectif actuel
                $nouvelEffectif = $record->nb_animaux_actuel - $data['nb_morts'];
                $record->update([
                    'nb_animaux_actuel' => max(0, $nouvelEffectif),
                ]);

                // Créer un événement sanitaire pour traçabilité
                EvenementsSanitaire::create([
                    'lot_id' => $record->id,
                    'animal_id' => null,
                    'type_evenement' => 'mortalite',
                    'date_evenement' => $data['date_heure'],
                    'description' => "Mortalité de {$data['nb_morts']} animal(aux). Cause : "
                        .($data['cause'] ?? 'non renseignée')
                        .($data['notes'] ? ". Notes : {$data['notes']}" : ''),
                    'intervenant' => auth()->user()->name ?? 'Système',
                ]);

                // Créer une tâche si mortalité anormale
                if (($data['creer_tache'] ?? 'non') === 'oui') {
                    Tache::create([
                        'lot_id' => $record->id,
                        'titre' => "Suivi mortalité lot {$record->numero_lot}",
                        'description' => "Mortalité de {$data['nb_morts']} animaux détectée. Cause : "
                            .($data['cause'] ?? 'inconnue')
                            .". Surveillance renforcée recommandée.",
                        'priorite' => 'haute',
                        'statut' => 'a_faire',
                        'date_echeance' => now()->addDays(2),
                    ]);
                }

                // Calculer le nouveau taux de mortalité
                $tauxMortalite = $record->fresh()->taux_mortalite;

                // Notification avec alerte si taux élevé
                $notification = Notification::make()
                    ->title('Mortalité enregistrée')
                    ->body("{$data['nb_morts']} animal(aux) déclaré(s). Nouvel effectif : {$nouvelEffectif}. Taux de mortalité : ".number_format($tauxMortalite, 2).' %');

                if ($tauxMortalite >= 5) {
                    $notification->warning()->persistent();
                } else {
                    $notification->success();
                }

                $notification->send();
            })
            ->after(fn () => redirect()->back());
    }
}
