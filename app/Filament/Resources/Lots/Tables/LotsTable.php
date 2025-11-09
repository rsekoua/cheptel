<?php

namespace App\Filament\Resources\Lots\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                    ->label('N° Lot')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type_lot')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'post_sevrage' => 'info',
                        'engraissement' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'post_sevrage' => 'Post-sevrage',
                        'engraissement' => 'Engraissement',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('statut_lot')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'actif' => 'success',
                        'termine' => 'gray',
                        'vide' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'actif' => 'Actif',
                        'termine' => 'Terminé',
                        'vide' => 'Vide',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('date_creation')
                    ->label('Date création')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('nb_animaux_actuel')
                    ->label('Effectif')
                    ->numeric()
                    ->sortable()
                    ->suffix(' animaux')
                    ->alignCenter()
                    ->description(fn ($record) => $record->nb_animaux_depart ? "Départ: {$record->nb_animaux_depart}" : null),

                TextColumn::make('poids_moyen_actuel_kg')
                    ->label('Poids moyen')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable()
                    ->alignCenter()
                    ->description(fn ($record) => $record->poids_moyen_depart_kg ? "Départ: {$record->poids_moyen_depart_kg} kg" : null),

                TextColumn::make('taux_mortalite')
                    ->label('Mortalité')
                    ->state(fn ($record) => $record->taux_mortalite)
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state < 3 => 'success',
                        $state < 5 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => $state !== null ? number_format($state, 2).' %' : '-')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('gmq')
                    ->label('GMQ')
                    ->state(fn ($record) => $record->gmq)
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 700 => 'success',
                        $state >= 600 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => $state !== null ? $state.' g/j' : '-')
                    ->tooltip('Gain Moyen Quotidien en grammes par jour')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('salle.nom')
                    ->label('Salle')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('planAlimentation.nom')
                    ->label('Plan alimentation')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('portees')
                    ->label('Portées')
                    ->badge()
                    ->state(function ($record) {
                        $porteesCount = $record->portees()->count();

                        return $porteesCount > 0 ? "{$porteesCount} portée(s)" : 'Aucune';
                    })
                    ->color(fn ($record) => $record->portees()->count() > 0 ? 'success' : 'gray')
                    ->description(function ($record) {
                        $portees = $record->portees()->with('animal')->get();

                        if ($portees->isEmpty()) {
                            return null;
                        }

                        $details = $portees->take(3)->map(function ($portee) {
                            $truie = $portee->animal?->numero_identification ?? 'N/A';
                            $pivot = $portee->pivot;

                            if ($pivot && $pivot->nb_porcelets_transferes) {
                                return "{$truie}: {$pivot->nb_porcelets_transferes} porcelets";
                            }

                            return "{$truie}: portée complète";
                        })->join(', ');

                        if ($portees->count() > 3) {
                            $details .= '...';
                        }

                        return $details;
                    })
                    ->tooltip(function ($record) {
                        $portees = $record->portees()->with('animal')->get();

                        if ($portees->isEmpty()) {
                            return 'Aucune portée affectée à ce lot';
                        }

                        return $portees->map(function ($portee) {
                            $truie = $portee->animal?->numero_identification ?? 'N/A';
                            $pivot = $portee->pivot;

                            if ($pivot && $pivot->nb_porcelets_transferes) {
                                $details = "{$pivot->nb_porcelets_transferes} porcelets";

                                if ($pivot->poids_total_transfere_kg) {
                                    $details .= " ({$pivot->poids_total_transfere_kg} kg)";
                                }

                                return "Truie {$truie}: {$details}";
                            }

                            return "Truie {$truie}: portée complète";
                        })->join("\n");
                    })
                    ->toggleable(),

                TextColumn::make('date_derniere_pesee')
                    ->label('Dernière pesée')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('date_sortie')
                    ->label('Date sortie')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('prix_vente_total')
                    ->label('Prix vente')
                    ->money('EUR')
                    ->sortable()
                    ->placeholder('-')
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
                    ])
                    ->multiple(),

                SelectFilter::make('statut_lot')
                    ->label('Statut')
                    ->options([
                        'actif' => 'Actif',
                        'termine' => 'Terminé',
                        'vide' => 'Vide',
                    ])
                    ->multiple(),

                SelectFilter::make('salle_id')
                    ->label('Salle')
                    ->relationship('salle', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),
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
            ->defaultSort('date_creation', 'desc');
    }
}
