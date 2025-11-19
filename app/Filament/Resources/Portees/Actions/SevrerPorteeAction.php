<?php

namespace App\Filament\Resources\Portees\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class SevrerPorteeAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'sevrer')
            ->label('Sevrage')
            ->icon(Heroicon::ArrowRightCircle)
            ->color('warning')
            ->visible(fn ($record): bool => $record->date_sevrage === null)
            ->modalHeading('Enregistrer le sevrage')
            ->modalDescription('Saisissez les informations concernant le sevrage de cette portée')
            ->modalSubmitActionLabel('Enregistrer')
            ->form([
                DatePicker::make('date_sevrage')
                    ->label('Date de sevrage')
                    ->required()
                    ->native(false)
                    ->maxDate(now())
                    ->default(now())
                    ->minDate(fn ($record) => $record->date_mise_bas)
                    ->helperText('Généralement 21-28 jours après la mise bas'),

                TextInput::make('nb_sevres')
                    ->label('Nombre de sevrés')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(fn ($record) => $record->nb_total)
                    ->suffix(' porcelets')
                    ->helperText('Nombre de porcelets vivants au moment du sevrage')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Calculer le poids moyen si poids total est renseigné
                        $nbSevres = (int) ($state ?? 0);
                        $poidsTotal = (float) ($get('poids_total_sevrage_kg') ?? 0);

                        if ($nbSevres > 0 && $poidsTotal > 0) {
                            $set('poids_moyen_sevrage_kg', round($poidsTotal / $nbSevres, 2));
                        }
                    }),

                TextInput::make('poids_total_sevrage_kg')
                    ->label('Poids total au sevrage')
                    ->numeric()
                    ->suffix(' kg')
                    ->step(0.01)
                    ->minValue(0)
                    ->helperText('Poids total cumulé de tous les porcelets sevrés')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Calculer le poids moyen
                        $poidsTotal = (float) ($state ?? 0);
                        $nbSevres = (int) ($get('nb_sevres') ?? 0);

                        if ($nbSevres > 0 && $poidsTotal > 0) {
                            $set('poids_moyen_sevrage_kg', round($poidsTotal / $nbSevres, 2));
                        }
                    }),

                TextInput::make('poids_moyen_sevrage_kg')
                    ->label('Poids moyen au sevrage')
                    ->numeric()
                    ->suffix(' kg')
                    ->step(0.01)
                    ->minValue(0)
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Calculé automatiquement : Poids total ÷ Nombre de sevrés'),

                Select::make('lot_destination_id')
                    ->label('Lot de destination')
                    ->relationship('lotDestination', 'numero_lot')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->helperText('Lot où les porcelets seront transférés après sevrage'),
            ])
            ->action(function ($record, array $data) {
                // Mettre à jour la portée avec les données de sevrage
                $record->update([
                    'date_sevrage' => $data['date_sevrage'],
                    'nb_sevres' => $data['nb_sevres'],
                    'poids_total_sevrage_kg' => $data['poids_total_sevrage_kg'] ?? null,
                    'poids_moyen_sevrage_kg' => $data['poids_moyen_sevrage_kg'] ?? null,
                    'lot_destination_id' => $data['lot_destination_id'] ?? null,
                ]);

                // Mettre à jour le statut du cycle de reproduction à "terminé avec succès"
                if ($record->cycleReproduction) {
                    $record->cycleReproduction->update([
                        'statut_cycle' => 'termine_succes',
                    ]);
                }

                Notification::make()
                    ->title('Sevrage enregistré')
                    ->body("Le sevrage de {$data['nb_sevres']} porcelets a été enregistré avec succès.")
                    ->success()
                    ->send();
            });
    }
}
