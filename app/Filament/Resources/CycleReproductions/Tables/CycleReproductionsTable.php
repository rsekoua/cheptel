<?php

namespace App\Filament\Resources\CycleReproductions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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
                    ->sortable(),

                TextColumn::make('numero_cycle')
                    ->label('N° Cycle')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('date_debut')
                    ->label('Date de début')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('statut_cycle')
                    ->label('Statut')
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
                    })
                    ->sortable(),

//                TextColumn::make('date_premiere_saillie')
//                    ->label('1ère saillie')
//                    ->dateTime('d/m/Y H:i')
//                    ->sortable()
//                    ->placeholder('-'),
//
//                TextColumn::make('type_saillie')
//                    ->label('Type')
//                    ->badge()
//                    ->formatStateUsing(fn (?string $state): string => match ($state) {
//                        'IA' => 'Insémination Artificielle',
//                        'MN' => 'Monte Naturelle',
//                        default => '-',
//                    })
//                    ->color(fn (?string $state): string => match ($state) {
//                        'IA' => 'info',
//                        'MN' => 'success',
//                        default => 'gray',
//                    })
//                    ->placeholder('-'),

                TextColumn::make('saillies_count')
                    ->label('Nb saillies')
                    ->counts('saillies')
                    ->badge()
                    ->color('info'),

                TextColumn::make('date_diagnostic')
                    ->label('Date diagnostic')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('resultat_diagnostic')
                    ->label('Résultat')
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
                    })
                    ->toggleable(),

                TextColumn::make('date_mise_bas_prevue')
                    ->label('Mise-bas prévue')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('date_mise_bas_reelle')
                    ->label('Mise-bas réelle')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

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
                //
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
