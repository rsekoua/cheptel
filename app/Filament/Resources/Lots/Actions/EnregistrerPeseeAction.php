<?php

namespace App\Filament\Resources\Lots\Actions;

use App\Models\Pesee;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class EnregistrerPeseeAction
{
    public static function make(): Action
    {
        return Action::make('enregistrer_pesee')
            ->label('Enregistrer une pesée')
            ->icon(Heroicon::OutlinedScale)
            ->color('success')
            ->visible(fn ($record) => $record->statut_lot === 'actif')
            ->schema([
                DateTimePicker::make('date_heure')
                    ->label('Date et heure de la pesée')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->default(now())
                    ->maxDate(now()),

                Select::make('methode')
                    ->label('Méthode de pesée')
                    ->required()
                    ->options([
                        'collective' => 'Collective (tous les animaux)',
                        'echantillon' => 'Échantillon représentatif',
                    ])
                    ->default('collective')
                    ->native(false)
                    ->live()
                    ->helperText('Collective = pesée de tous les animaux, Échantillon = pesée d\'un groupe représentatif'),

                TextInput::make('nb_animaux_peses')
                    ->label('Nombre d\'animaux pesés')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('animaux')
                    ->default(fn ($get, $record) => $get('methode') === 'collective' ? $record->nb_animaux_actuel : null)
                    ->disabled(fn ($get) => $get('methode') === 'collective')
                    ->dehydrated()
                    ->helperText(fn ($get) => $get('methode') === 'collective'
                        ? 'Automatiquement égal à l\'effectif actuel'
                        : 'Nombre d\'animaux inclus dans l\'échantillon'),

                TextInput::make('poids_total_mesure_kg')
                    ->label('Poids total mesuré')
                    ->required()
                    ->numeric()
                    ->suffix('kg')
                    ->step(0.01)
                    ->minValue(0.01)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $nbAnimaux = $get('nb_animaux_peses');
                        if ($nbAnimaux && $nbAnimaux > 0 && $state) {
                            $poidsMoyen = $state / $nbAnimaux;
                            $set('poids_moyen_mesure_kg', round($poidsMoyen, 2));
                        }
                    })
                    ->helperText('Poids total cumulé de tous les animaux pesés'),

                TextInput::make('poids_moyen_mesure_kg')
                    ->label('Poids moyen calculé')
                    ->numeric()
                    ->suffix('kg')
                    ->step(0.01)
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Calculé automatiquement : poids total / nombre d\'animaux'),

                TextInput::make('notes')
                    ->label('Notes')
                    ->maxLength(255)
                    ->placeholder('Observations sur la pesée (optionnel)')
                    ->columnSpanFull(),
            ])
            ->action(function ($record, array $data) {
                // Créer l'enregistrement de pesée
                Pesee::create([
                    'lot_id' => $record->id,
                    'animal_id' => null, // Pesée collective
                    'date_heure' => $data['date_heure'],
                    'poids_kg' => $data['poids_moyen_mesure_kg'],
                    'type_pesee' => $data['methode'],
                    'notes' => $data['notes'] ?? null,
                ]);

                // Mettre à jour les données du lot
                $record->update([
                    'poids_total_actuel_kg' => $data['poids_total_mesure_kg'],
                    'poids_moyen_actuel_kg' => $data['poids_moyen_mesure_kg'],
                    'date_derniere_pesee' => $data['date_heure'],
                ]);

                // Notification avec GMQ si disponible
                $gmq = $record->fresh()->gmq;
                $gmqText = $gmq ? " GMQ : {$gmq} g/jour." : '';

                Notification::make()
                    ->success()
                    ->title('Pesée enregistrée avec succès')
                    ->body("Poids moyen : {$data['poids_moyen_mesure_kg']} kg.{$gmqText}")
                    ->send();
            })
            ->after(fn () => redirect()->back());
    }
}
