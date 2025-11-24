<?php

namespace App\Filament\Resources\StockAliments\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StockAlimentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label('Aliment')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->description),

                TextColumn::make('stockEntrepot.poids_kg_disponible')
                    ->label('Stock entrepôt')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->placeholder('0.00 kg')
                    ->color(function ($record) {
                        $stock = $record->stockEntrepot?->poids_kg_disponible ?? 0;

                        return $stock < $record->seuil_alerte ? 'danger' : 'success';
                    })
                    ->weight(function ($record) {
                        $stock = $record->stockEntrepot?->poids_kg_disponible ?? 0;

                        return $stock < $record->seuil_alerte ? 'bold' : 'regular';
                    })
                    ->description(function ($record) {
                        $valeur = $record->stockEntrepot?->valeur_stock ?? 0;

                        return number_format($valeur, 0, ',', ' ').' FCFA';
                    }),

                TextColumn::make('stockPreparation.poids_kg_disponible')
                    ->label('Stock préparation')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->placeholder('0.00 kg')
                    ->color('primary')
                    ->description(function ($record) {
                        $valeur = $record->stockPreparation?->valeur_stock ?? 0;

                        return number_format($valeur, 0, ',', ' ').' FCFA';
                    }),

                TextColumn::make('stock_total')
                    ->label('Stock total')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(query: function ($query, $direction) {
                        return $query->withSum('stocks as stock_total', 'poids_kg_disponible')
                            ->orderBy('stock_total', $direction);
                    })
                    ->state(function ($record) {
                        $entrepot = $record->stockEntrepot?->poids_kg_disponible ?? 0;
                        $preparation = $record->stockPreparation?->poids_kg_disponible ?? 0;

                        return $entrepot + $preparation;
                    })
                    ->weight('bold')
                    ->description(function ($record) {
                        $valeurEntrepot = $record->stockEntrepot?->valeur_stock ?? 0;
                        $valeurPreparation = $record->stockPreparation?->valeur_stock ?? 0;
                        $valeurTotale = $valeurEntrepot + $valeurPreparation;

                        return number_format($valeurTotale, 0, ',', ' ').' FCFA';
                    }),

                TextColumn::make('seuil_alerte')
                    ->label('Seuil alerte')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stockEntrepot.cout_moyen_kg')
                    ->label('Coût moyen/kg')
                    ->suffix(' FCFA')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('alerte_stock')
                    ->label('État du stock')
                    ->placeholder('Tous les aliments')
                    ->trueLabel('Stock en alerte')
                    ->falseLabel('Stock suffisant')
                    ->queries(
                        true: fn ($query) => $query->whereHas('stockEntrepot', function ($q) {
                            $q->whereRaw('poids_kg_disponible < (SELECT seuil_alerte FROM aliments WHERE aliments.id = stock_aliments.aliment_id)');
                        }),
                        false: fn ($query) => $query->whereHas('stockEntrepot', function ($q) {
                            $q->whereRaw('poids_kg_disponible >= (SELECT seuil_alerte FROM aliments WHERE aliments.id = stock_aliments.aliment_id)');
                        }),
                    ),

                SelectFilter::make('actif')
                    ->label('Statut')
                    ->options([
                        1 => 'Actifs',
                        0 => 'Inactifs',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('nom');
    }
}
