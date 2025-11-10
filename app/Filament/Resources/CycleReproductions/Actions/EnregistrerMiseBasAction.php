<?php

namespace App\Filament\Resources\CycleReproductions\Actions;

use App\Filament\Resources\Portees\PorteeResource;
use App\Models\Portee;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class EnregistrerMiseBasAction
{
    public static function make(): Action
    {
        return Action::make('enregistrer_mise_bas')
            ->label('Enregistrer la mise-bas #')
            ->icon(Heroicon::OutlinedPlusCircle)
            ->color('success')
            ->visible(fn ($record) => ! $record->portee()->exists()
                && $record->statut_cycle === 'en_cours'
                && $record->resultat_diagnostic === 'positif')
            ->disabled(fn ($record) => $record->resultat_diagnostic !== 'positif')
            ->tooltip(fn ($record) => $record->resultat_diagnostic !== 'positif'
                ? 'Le diagnostic de gestation doit être positif avant d\'enregistrer une mise-bas'
                : null)
            ->schema([
                DateTimePicker::make('date_mise_bas')
                    ->label('Date et heure de mise-bas')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->default(fn ($record) => $record->date_mise_bas_prevue ?? now())
                    ->helperText('Date prévue calculée automatiquement, vous pouvez l\'ajuster'),

                TextInput::make('nb_nes_vifs')
                    ->label('Nombre de nés vivants')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('porcelets')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $nbMortNes = $get('nb_mort_nes') ?? 0;
                        $nbMomifies = $get('nb_momifies') ?? 0;
                        $total = ($state ?? 0) - $nbMortNes - $nbMomifies;
                        $set('nb_total', $total);
                    }),

                TextInput::make('nb_mort_nes')
                    ->label('Nombre de mort-nés')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('porcelets')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $nbNesVifs = $get('nb_nes_vifs') ?? 0;
                        $nbMomifies = $get('nb_momifies') ?? 0;
                        $total = $nbNesVifs - ($state ?? 0) - $nbMomifies;
                        $set('nb_total', $total);
                    }),

                TextInput::make('nb_momifies')
                    ->label('Nombre de momifiés')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('porcelets')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $nbNesVifs = $get('nb_nes_vifs') ?? 0;
                        $nbMortNes = $get('nb_mort_nes') ?? 0;
                        $total = $nbNesVifs - $nbMortNes - ($state ?? 0);
                        $set('nb_total', $total);
                    }),

                TextInput::make('nb_total')
                    ->label('Nombre total vivants')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->suffix('porcelets')
                    ->helperText('Calculé automatiquement'),

                TextInput::make('poids_moyen_naissance_g')
                    ->label('Poids moyen à la naissance (optionnel)')
                    ->numeric()
                    ->suffix('g')
                    ->step(1)
                    ->minValue(0)
                    ->helperText('Généralement entre 1200g et 1600g'),
            ])
            ->action(function ($record, array $data) {
                // Créer la portée automatiquement
                $portee = Portee::create([
                    'cycle_reproduction_id' => $record->id,
                    'animal_id' => $record->animal_id,
                    'date_mise_bas' => $data['date_mise_bas'],
                    'nb_nes_vifs' => $data['nb_nes_vifs'],
                    'nb_mort_nes' => $data['nb_mort_nes'],
                    'nb_momifies' => $data['nb_momifies'],
                    'nb_total' => $data['nb_total'],
                    'poids_moyen_naissance_g' => $data['poids_moyen_naissance_g'] ?? null,
                ]);

                // Mettre à jour la date de mise-bas réelle du cycle
                $record->update([
                    'date_mise_bas_reelle' => $data['date_mise_bas'],
                ]);

                // Stocker l'ID de la portée et le nombre total pour la redirection et notification
                session()->put('portee_id', $portee->id);
                session()->put('nb_total', $portee->nb_total);
            })
            ->successNotification(
                fn ($record) => Notification::make()
                    ->success()
                    ->title('Mise-bas enregistrée avec succès')
                    ->body('La portée a été créée avec '.session()->get('nb_total', 0).' porcelets.')
                    ->actions([
                        Action::make('view_portee')
                            ->label('Voir la portée')
                            ->url(fn () => PorteeResource::getUrl('view', ['record' => session()->get('portee_id')]))
                            ->button(),
                    ])
            )
            ->successRedirectUrl(function () {
                // Rediriger vers la portée créée
                $porteeId = session()->pull('portee_id');
                if ($porteeId) {
                    return PorteeResource::getUrl('view', ['record' => $porteeId]);
                }

                return null;
            });
    }
}
