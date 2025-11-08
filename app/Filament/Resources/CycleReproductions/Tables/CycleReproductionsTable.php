<?php

namespace App\Filament\Resources\CycleReproductions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CycleReproductionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('animal.numero_identification')
                    ->label('Animal')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->animal?->type_animal),

                TextColumn::make('numero_cycle')
                    ->label('N° Cycle')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('date_debut')
                    ->label('Date début')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('statut_cycle')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'en_cours' => 'info',
                        'termine_succes' => 'success',
                        'termine_echec' => 'danger',
                        'avorte' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en_cours' => 'En cours',
                        'termine_succes' => 'Terminé (succès)',
                        'termine_echec' => 'Terminé (échec)',
                        'avorte' => 'Avorté',
                        default => $state,
                    }),

                TextColumn::make('resultat_diagnostic')
                    ->label('Diagnostic')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'positif' => 'success',
                        'negatif' => 'danger',
                        'en_attente' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'positif' => 'Gestante',
                        'negatif' => 'Vide',
                        'en_attente' => 'En attente',
                        default => $state,
                    }),

                TextColumn::make('saillies_count')
                    ->label('Nb saillies')
                    ->counts('saillies')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('saillies')
                    ->label('Première saillie')
                    ->formatStateUsing(function ($record) {
                        $premiereSaillie = $record->saillies()->orderBy('date_heure')->first();
                        if (! $premiereSaillie) {
                            return '-';
                        }

                        return $premiereSaillie->date_heure->format('d/m/Y H:i').' ('.$premiereSaillie->type.')';
                    })
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('date_diagnostic')
                    ->label('Date diagnostic')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('date_mise_bas_prevue')
                    ->label('Mise-bas prévue')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('date_mise_bas_reelle')
                    ->label('Mise-bas réelle')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('statut_cycle')
                    ->label('Statut')
                    ->options([
                        'en_cours' => 'En cours',
                        'termine_succes' => 'Terminé (succès)',
                        'termine_echec' => 'Terminé (échec)',
                        'avorte' => 'Avorté',
                    ])
                    ->multiple(),

                SelectFilter::make('resultat_diagnostic')
                    ->label('Diagnostic')
                    ->options([
                        'en_attente' => 'En attente',
                        'positif' => 'Positif (gestante)',
                        'negatif' => 'Négatif (vide)',
                    ])
                    ->multiple(),

                SelectFilter::make('type_saillie')
                    ->label('Type saillie')
                    ->options([
                        'IA' => 'IA (Insémination Artificielle)',
                        'MN' => 'MN (Monte Naturelle)',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_debut', 'desc');
    }
}
