<?php

namespace App\Filament\Resources\CycleReproductions\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CycleReproductionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Section::make('Informations du cycle')
                    ->description('Informations générales sur le cycle de reproduction')
                    ->icon(Heroicon::ArrowPath)
                    ->schema([
                        TextEntry::make('animal.numero_identification')
                            ->label('Animal'),

                        TextEntry::make('numero_cycle')
                            ->label('Numéro de cycle')
                            ->numeric(),

                        TextEntry::make('date_debut')
                            ->label('Date de début du cycle')
                            ->date('d/m/Y'),

                        TextEntry::make('statut_cycle')
                            ->label('Statut du cycle')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'gestation_en_cours' => 'Gestation en cours',
                                'mise_bas_effectuee' => 'Mise-bas effectuée',
                                'echec_gestation' => 'Échec de gestation',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'gestation_en_cours' => 'warning',
                                'mise_bas_effectuee' => 'success',
                                'echec_gestation' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                Section::make('Diagnostic de gestation')
                    ->description('Résultats du diagnostic de gestation')
                    ->icon(Heroicon::Beaker)
                    ->schema([
                        TextEntry::make('date_diagnostic')
                            ->label('Date du diagnostic')
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('resultat_diagnostic')
                            ->label('Résultat du diagnostic')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'positif' => 'Positif (gestante)',
                                'negatif' => 'Négatif (vide)',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'positif' => 'success',
                                'negatif' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2)
                    ->columnSpan(1)
                    ->visible(fn ($record): bool => ! empty($record->date_premiere_saillie)),

                Section::make('Mise-bas')
                    ->description('Dates prévisionnelle et réelle de mise-bas')
                    ->icon(Heroicon::Cake)
                    ->schema([
                        TextEntry::make('date_mise_bas_prevue')
                            ->label('Date de mise-bas prévue')
                            ->date('d/m/Y')
                            ->placeholder('-'),
                           // ->helperText('Calculée automatiquement (saillie + 114 jours)'),

                        TextEntry::make('date_mise_bas_reelle')
                            ->label('Date de mise-bas réelle')
                            ->date('d/m/Y')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpan(1)
                    ->visible(fn ($record): bool => ! empty($record->date_premiere_saillie)),

                Section::make('Saillies / Inséminations')
                    //->description('Liste des saillies effectuées')
                    ->icon(Heroicon::Heart)
                    ->schema([
//                        TextEntry::make('date_premiere_saillie')
//                            ->label('Date de la première saillie')
//                            ->dateTime('d/m/Y H:i')
//                            ->placeholder('-'),

//                        TextEntry::make('type_saillie')
//                            ->label('Type principal de saillie')
//                            ->badge()
//                            ->formatStateUsing(fn (?string $state): string => match ($state) {
//                                'IA' => 'Insémination Artificielle',
//                                'MN' => 'Monte Naturelle',
//                                default => '-',
//                            })
//                            ->color(fn (?string $state): string => match ($state) {
//                                'IA' => 'info',
//                                'MN' => 'success',
//                                default => 'gray',
//                            })
//                            ->placeholder('-'),

                        RepeatableEntry::make('saillies')
                            ->label('Liste des saillies effectuées')
                            ->schema([
                                TextEntry::make('type')
                                    ->label('Type')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'IA' => 'IA',
                                        'MN' => 'MN',
                                        default => $state,
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        'IA' => 'info',
                                        'MN' => 'success',
                                        default => 'gray',
                                    }),

                                TextEntry::make('date_heure')
                                    ->label('Date et heure')
                                    ->dateTime('d/m/Y H:i'),

                                TextEntry::make('verrat.numero_identification')
                                    ->label('Verrat')
                                    ->placeholder('-'),

                                TextEntry::make('semence_lot_numero')
                                    ->label('N° lot de semence')
                                    ->placeholder('-'),

                                TextEntry::make('intervenant')
                                    ->label('Intervenant')
                                    ->placeholder('-'),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(4)
                    ->collapsible()
                    ->visible(fn ($record): bool => $record->saillies()->exists()),

                Section::make('Informations complémentaires')
                    ->description('Motif d\'échec et notes')
                    ->icon(Heroicon::DocumentText)
                    ->schema([
                        TextEntry::make('motif_echec')
                            ->label('Motif d\'échec')
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->visible(fn ($record): bool => $record->statut_cycle === 'echec_gestation'),

                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),

//                Section::make('Horodatage')
//                    ->description('Dates de création et modification')
//                    ->icon(Heroicon::Clock)
//                    ->schema([
//                        TextEntry::make('created_at')
//                            ->label('Créé le')
//                            ->dateTime('d/m/Y H:i'),
//
//                        TextEntry::make('updated_at')
//                            ->label('Modifié le')
//                            ->dateTime('d/m/Y H:i'),
//                    ])
//                    ->columns(2)
//                    ->columnSpan(2)
//                    ->collapsed(),
            ]);
    }
}
