<?php

namespace App\Filament\Resources\CycleReproductions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CycleReproductionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextEntry::make('animal.numero_identification')
                            ->label('Animal'),

                        TextEntry::make('numero_cycle')
                            ->label('Numéro de cycle'),

                        TextEntry::make('date_debut')
                            ->label('Date de début')
                            ->date('d/m/Y'),

                        TextEntry::make('statut_cycle')
                            ->label('Statut du cycle')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'en_cours' => 'info',
                                'termine_succes' => 'success',
                                'termine_echec' => 'danger',
                                'avorte' => 'warning',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'en_cours' => 'En cours',
                                'termine_succes' => 'Terminé avec succès',
                                'termine_echec' => 'Terminé en échec',
                                'avorte' => 'Avorté',
                                default => $state,
                            }),
                    ])
                    ->columns(2),

                Section::make('Chaleurs et saillie')
                    ->schema([
                        TextEntry::make('date_chaleurs')
                            ->label('Date des chaleurs')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('date_premiere_saillie')
                            ->label('Date de première saillie')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('type_saillie')
                            ->label('Type de saillie')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'IA' => 'Insémination Artificielle (IA)',
                                'MN' => 'Monte Naturelle (MN)',
                                default => '-',
                            }),
                    ])
                    ->columns(3),

                Section::make('Diagnostic de gestation')
                    ->schema([
                        TextEntry::make('date_diagnostic')
                            ->label('Date du diagnostic')
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('resultat_diagnostic')
                            ->label('Résultat du diagnostic')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'positif' => 'success',
                                'negatif' => 'danger',
                                'en_attente' => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'positif' => 'Positif (gestante)',
                                'negatif' => 'Négatif (vide)',
                                'en_attente' => 'En attente',
                                default => $state,
                            }),
                    ])
                    ->columns(2),

                Section::make('Mise-bas')
                    ->schema([
                        TextEntry::make('date_mise_bas_prevue')
                            ->label('Date de mise-bas prévue')
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('date_mise_bas_reelle')
                            ->label('Date de mise-bas réelle')
                            ->date('d/m/Y')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Échec et notes')
                    ->schema([
                        TextEntry::make('motif_echec')
                            ->label('Motif d\'échec')
                            ->columnSpanFull()
                            ->placeholder('-'),

                        TextEntry::make('notes')
                            ->label('Notes')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])
                    ->collapsed(),

                Section::make('Informations système')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Modifié le')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
