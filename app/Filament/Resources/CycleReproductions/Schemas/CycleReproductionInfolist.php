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
            ->columns(2)
            ->components([
                // Section principale: Cycle
                Section::make('Cycle de reproduction')
                    ->icon(Heroicon::ArrowPath)
                    ->schema([
                        TextEntry::make('animal.numero_identification')
                            ->label('Truie')
                            ->color('danger')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('saillies')
                            ->label('Verrat')
                            ->color('info')
                            ->weight('bold')
                            ->size('lg')
                            ->state(fn ($record) => $record->saillies()->first()?->verrat?->numero_identification ?? '-'),
                        //                        TextEntry::make('date_premiere_saillie')
                        //                            ->label('1ère saillie')
                        //                            ->dateTime('d/m/Y'),
                        TextEntry::make('date_mise_bas_prevue_formatee')
                            ->label('Date prévue')
                            ->tooltip(fn ($record): ?string => $record->date_mise_bas_prevue?->format('d/m/Y'))
                            ->placeholder('-'),

                        TextEntry::make('date_mise_bas_reelle')
                            ->label('Date réelle')
                            ->dateTime('d/m/Y')
                           // ->tooltip(fn ($record): ?string => $record->date_mise_bas_reelle?->format('d/m/Y'))
                            ->placeholder('-'),

                        TextEntry::make('statut_cycle')
                            ->label('Statut')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'en_attente' => 'En attente',
                                'gestation_en_cours' => 'Gestation en cours',
                                'mise_bas_effectuee' => 'Mise-bas effectuée',
                                'en_lactation' => 'En lactation',
                                'termine_succes' => 'Terminé avec succès',
                                'termine_echec' => 'Terminé avec échec',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'gestation_en_cours' => 'warning',
                                'mise_bas_effectuee', 'termine_succes' => 'success',
                                'termine_echec' => 'danger',
                                'en_lactation' => 'info',
                                default => 'gray',
                            }),

                        //                        TextEntry::make('numero_cycle')
                        //                            ->label('N° cycle'),

                    ])
                    ->columns([
                        'default' => 2,
                        'lg' => 4,
                    ])
                    ->columnSpan(1),

                //
                //                // Section Mise-bas
                //                Section::make('Mise-bas')
                //                    ->icon(Heroicon::Cake)
                //                    ->schema([
                //                        TextEntry::make('date_mise_bas_prevue')
                //                            ->label('Date prévue')
                //                            ->date('d/m/Y')
                //                            ->placeholder('-'),
                //
                //                        TextEntry::make('date_mise_bas_reelle')
                //                            ->label('Date réelle')
                //                            ->date('d/m/Y')
                //                            ->placeholder('-'),
                //                    ])
                //                    ->columns([
                //                        'default' => 2,
                //                    ])
                //                    ->columnSpan(1)
                //                    ->visible(fn ($record): bool => ! empty($record->date_mise_bas_prevue) && $record->statut_cycle !== 'termine_echec'),

                // Section Portée (visible si succès)
                Section::make('Portée')
                    ->icon(Heroicon::UserGroup)
                    ->schema([
                        TextEntry::make('portee.nb_total')
                            ->label('Total nés')
                            ->numeric()
                            ->suffix(' porcelets')
                            ->color('success')
                            ->weight('bold')
                            ->size('lg'),
                        //
                        //                        TextEntry::make('portee.nb_nes_vifs')
                        //                            ->label('Nés vifs')
                        //                            ->numeric()
                        //                            ->color('success'),

                        TextEntry::make('portee.nb_mort_nes')
                            ->label('Mort-nés')
                            ->numeric()
                            ->color('danger'),

                        //                        TextEntry::make('portee.poids_moyen_naissance_g')
                        //                            ->label('Poids moyen')
                        //                            ->numeric()
                        //                            ->suffix(' g'),

                        TextEntry::make('portee.date_sevrage')
                            ->label('Date sevrage')
                            ->date('d/m/Y')
                            ->placeholder('Non sevré'),

                        TextEntry::make('portee.nb_sevres')
                            ->label('Nb sevrés')
                            ->numeric()
                            ->placeholder('-'),
                        //
                        //                        TextEntry::make('portee.poids_moyen_sevrage_kg')
                        //                            ->label('Poids moyen sevrage')
                        //                            ->numeric()
                        //                            ->suffix(' kg')
                        //                            ->placeholder('-'),
                        //
                        //                        TextEntry::make('portee.lotDestination.numero')
                        //                            ->label('Lot destination')
                        //                            ->placeholder('-'),
                    ])
                    ->columns([
                        'default' => 2,
                        'md' => 4,
                    ])
                    ->columnSpanFull()
                    ->visible(fn ($record): bool => $record->statut_cycle === 'termine_succes' || 'en_lactation' && $record->portee !== null),

                // Section Diagnostic
                Section::make('Diagnostic')
                    ->icon(Heroicon::Beaker)
                    ->schema([
                        TextEntry::make('date_debut')
                            ->label('Début du cycle')
                            ->date('d/m/Y'),
                        TextEntry::make('date_diagnostic')
                            ->label('Date')
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('resultat_diagnostic')
                            ->label('Résultat')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'positif' => 'Gestante',
                                'negatif' => 'Vide',
                                default => '-',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'positif' => 'success',
                                'negatif' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns([
                        'default' => 2,
                    ])
                    ->columnSpan(1)
                    ->visible(fn ($record): bool => ! empty($record->date_diagnostic)),

                // Section Liste des saillies (collapsible)
                //                Section::make('Détail des saillies')
                //                    ->icon(Heroicon::ListBullet)
                //                    ->schema([
                //                        RepeatableEntry::make('saillies')
                //                            ->label('')
                //                            ->schema([
                //                                TextEntry::make('type')
                //                                    ->label('Type')
                //                                    ->badge()
                //                                    ->formatStateUsing(fn (string $state): string => $state)
                //                                    ->color(fn (string $state): string => match ($state) {
                //                                        'IA' => 'info',
                //                                        'MN' => 'success',
                //                                        default => 'gray',
                //                                    }),
                //
                //                                TextEntry::make('date_heure')
                //                                    ->label('Date')
                //                                    ->dateTime('d/m/Y H:i'),
                //
                //                                TextEntry::make('verrat.numero_identification')
                //                                    ->label('Verrat')
                //                                    ->placeholder('-'),
                //
                //                                TextEntry::make('semence_lot_numero')
                //                                    ->label('Lot semence')
                //                                    ->placeholder('-'),
                //                            ])
                //                            ->columns(4)
                //                            ->columnSpanFull(),
                //                    ])
                //                    ->columnSpanFull()
                //                    ->collapsible()
                //                    ->collapsed()
                //                    ->visible(fn ($record): bool => $record->saillies()->count() > 1),

                // Section Saillies résumé
                Section::make('Saillie')
                    ->icon(Heroicon::Heart)
                    ->schema([
                        //                        TextEntry::make('saillies')
                        //                            ->label('Verrat')
                        //                            ->color('info')
                        //                            ->weight('bold')
                        //                            ->size('lg')
                        //                            ->state(fn ($record) => $record->saillies()->first()?->verrat?->numero_identification ?? '-'),

                        TextEntry::make('saillies')
                            ->label('Type')
                            ->badge()
                            ->state(fn ($record) => $record->saillies()->first()?->type)
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'IA' => 'Insémination',
                                'MN' => 'Monte naturelle',
                                default => '-',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'IA' => 'info',
                                'MN' => 'success',
                                default => 'gray',
                            }),

                        TextEntry::make('date_premiere_saillie')
                            ->label('1ère saillie')
                            ->dateTime('d/m/Y'),

                        TextEntry::make('saillies_count')
                            ->label('Nb saillies')
                            ->state(fn ($record) => $record->saillies()->count())
                            ->badge()
                            ->color('gray'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columns([
                        'default' => 2,
                    ])
                    ->columnSpan(1)
                    ->visible(fn ($record): bool => $record->saillies()->exists()),

                // Section Notes
                Section::make('Notes')
                    ->icon(Heroicon::DocumentText)
                    ->schema([
                        TextEntry::make('motif_echec')
                            ->label('Motif d\'échec')
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->visible(fn ($record): bool => $record->statut_cycle === 'termine_echec'),

                        TextEntry::make('notes')
                            ->label('Observations')
                            ->placeholder('Aucune note')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record): bool => ! empty($record->notes) || $record->statut_cycle === 'termine_echec'),
            ]);
    }
}
