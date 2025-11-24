<?php

namespace App\Filament\Resources\PreparationAliments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PreparationAlimentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date_preparation')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('nom')
                    ->label('Nom')
                    ->placeholder('Sans nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantite_totale')
                    ->label('Quantité totale')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('cout_total')
                    ->label('Coût total')
                    ->suffix(' FCFA')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('details_count')
                    ->label('Nb aliments')
                    ->counts('details')
                    ->badge()
                    ->sortable(),

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
            ->defaultSort('date_preparation', 'desc');
    }
}
