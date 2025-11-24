<?php

namespace App\Filament\Resources\MouvementAliments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MouvementAlimentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date_mouvement')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('type_mouvement')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'achat' => 'success',
                        'transfert_entree' => 'info',
                        'transfert_sortie' => 'warning',
                        'sortie_preparation' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'achat' => 'Achat',
                        'transfert_entree' => 'Vers préparation',
                        'transfert_sortie' => 'Vers entrepôt',
                        'sortie_preparation' => 'Sortie',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('aliment.nom')
                    ->label('Aliment')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type_stock')
                    ->label('Zone')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'entrepot' => 'Entrepôt',
                        'preparation' => 'Préparation',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('poids_kg')
                    ->label('Poids')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('valeur_mouvement')
                    ->label('Valeur')
                    ->suffix(' FCFA')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Créé par')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type_mouvement')
                    ->label('Type de mouvement')
                    ->options([
                        'achat' => 'Achat',
                        'transfert_entree' => 'Transfert vers préparation',
                        'transfert_sortie' => 'Transfert vers entrepôt',
                        'sortie_preparation' => 'Sortie pour utilisation',
                    ]),

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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_mouvement', 'desc');
    }
}
