<?php

namespace App\Filament\Resources\CycleReproductions\Actions;

use App\Models\Portee;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class MiseBasAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'mise_bas')
            ->label('')
            ->icon(Heroicon::Cake)
            ->modalWidth('sm')
            ->color('success')
            ->visible(fn ($record): bool => $record->statut_cycle === 'gestation_en_cours' && ! $record->portee)
            ->modalHeading('Enregistrer la mise bas')
            ->modalDescription('Saisissez les informations concernant la mise bas de cette portée')
            ->modalSubmitActionLabel('Enregistrer')
            ->Schema([
                DateTimePicker::make('date_mise_bas')
                    ->label('Date et heure de mise bas')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->maxDate(now())
                    ->default(now()),

                TextInput::make('nb_nes_vifs')
                    ->label('Nombre de nés vivants')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $nbNesVifs = (int) $state;
                        $nbMortNes = (int) ($get('nb_mort_nes') ?? 0);
                        $set('nb_total', $nbNesVifs - $nbMortNes);
                    }),

                TextInput::make('nb_mort_nes')
                    ->label('Nombre de mort-nés')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $nbMortNes = (int) $state;
                        $nbNesVifs = (int) ($get('nb_nes_vifs') ?? 0);
                        $set('nb_total', $nbNesVifs - $nbMortNes);
                    }),

                TextInput::make('nb_total')
                    ->label('Nombre total de porcelets')
                    ->numeric()
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->default(0),

//                TextInput::make('poids_moyen_naissance_g')
//                    ->label('Poids moyen à la naissance (grammes)')
//                    ->numeric()
//                    ->minValue(0)
//                    ->suffix('g')
//                    ->helperText('Poids moyen d\'un porcelet à la naissance'),

                Textarea::make('notes')
                    ->label('Notes')
                    ->placeholder('Observations sur la mise bas...')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->action(function ($record, array $data) {
                // Créer la portée
                $portee = Portee::create([
                    'cycle_reproduction_id' => $record->id,
                    'animal_id' => $record->animal_id,
                    'date_mise_bas' => $data['date_mise_bas'],
                    'nb_nes_vifs' => $data['nb_nes_vifs'],
                    'nb_mort_nes' => $data['nb_mort_nes'],
                    'nb_total' => $data['nb_total'],
                    'poids_moyen_naissance_g' => $data['poids_moyen_naissance_g'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                // Mettre à jour le statut du cycle et la date de mise bas réelle
                $record->update([
                    'statut_cycle' => 'en_lactation',
                    'date_mise_bas_reelle' => $data['date_mise_bas'],
                ]);

                Notification::make()
                    ->title('Mise bas enregistrée')
                    ->body("La portée de {$data['nb_total']} porcelets a été enregistrée avec succès.")
                    ->success()
                    ->send();
            });
    }
}
