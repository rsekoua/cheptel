<?php

namespace App\Filament\Resources\Pesees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PeseesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type_cible')
                    ->searchable(),
                TextColumn::make('animal.id')
                    ->searchable(),
                TextColumn::make('lot.id')
                    ->searchable(),
                TextColumn::make('date_pesee')
                    ->date()
                    ->sortable(),
                TextColumn::make('poids_total_kg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nb_animaux_peses')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('poids_moyen_kg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('methode')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
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
            ]);
    }
}
