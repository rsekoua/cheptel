<?php

namespace App\Filament\Resources\Lots\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_lot')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type_lot')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'post_sevrage' => 'Post-sevrage',
                        'engraissement' => 'Engraissement',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'post_sevrage' => 'info',
                        'engraissement' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('date_creation')
                    ->label('Date de création')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('portees_count')
                    ->label('Nb portées')
                    ->counts('portees')
                    ->sortable(),

                TextColumn::make('nb_animaux_depart')
                    ->label('Animaux départ')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('nb_animaux_actuel')
                    ->label('Animaux actuel')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('taux_mortalite')
                    ->label('Taux mortalité')
                    ->formatStateUsing(fn ($record) => $record->taux_mortalite ? number_format($record->taux_mortalite, 2).' %' : '-')
                    ->color(fn ($record) => match (true) {
                        $record->taux_mortalite === null => 'gray',
                        $record->taux_mortalite > 10 => 'danger',
                        $record->taux_mortalite > 5 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('statut_lot')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'actif' => 'Actif',
                        'transfere' => 'Transféré',
                        'vendu' => 'Vendu',
                        'cloture' => 'Clôturé',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'actif' => 'success',
                        'transfere' => 'warning',
                        'vendu' => 'info',
                        'cloture' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('date_sortie')
                    ->label('Date de sortie')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nb_animaux_sortie')
                    ->label('Animaux sortis')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('destination_sortie')
                    ->label('Destination')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('type_lot')
                    ->label('Type de lot')
                    ->options([
                        'post_sevrage' => 'Post-sevrage',
                        'engraissement' => 'Engraissement',
                    ]),

                SelectFilter::make('statut_lot')
                    ->label('Statut')
                    ->options([
                        'actif' => 'Actif',
                        'transfere' => 'Transféré',
                        'vendu' => 'Vendu',
                        'cloture' => 'Clôturé',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_creation', 'desc');
    }
}
