<?php

namespace App\Filament\Resources\StockAliments\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockAlimentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('aliment.nom')
                    ->label('Aliment')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type_stock')
                    ->label('Zone')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'entrepot' ? 'primary' : 'info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'entrepot' => 'Entrepôt',
                        'preparation' => 'Préparation',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('nombre_sacs_disponibles')
                    ->label('Sacs')
                    ->suffix(' sacs')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('poids_kg_disponible')
                    ->label('Poids')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->color(function ($record) {
                        if ($record->type_stock === 'entrepot' && $record->aliment) {
                            return $record->poids_kg_disponible < $record->aliment->seuil_alerte ? 'danger' : 'success';
                        }

                        return 'primary';
                    })
                    ->weight(function ($record) {
                        if ($record->type_stock === 'entrepot' && $record->aliment) {
                            return $record->poids_kg_disponible < $record->aliment->seuil_alerte ? 'bold' : 'regular';
                        }

                        return 'regular';
                    }),

                TextColumn::make('cout_moyen_kg')
                    ->label('Coût moyen/kg')
                    ->suffix(' FCFA')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('valeur_stock')
                    ->label('Valeur stock')
                    ->suffix(' FCFA')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('derniere_maj')
                    ->label('Dernière MAJ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type_stock')
                    ->label('Zone de stock')
                    ->options([
                        'entrepot' => 'Entrepôt',
                        'preparation' => 'Préparation',
                    ]),

                SelectFilter::make('aliment_id')
                    ->label('Aliment')
                    ->relationship('aliment', 'nom')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('aliment.nom');
    }
}
