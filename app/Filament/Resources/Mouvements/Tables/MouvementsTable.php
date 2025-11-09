<?php

namespace App\Filament\Resources\Mouvements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MouvementsTable
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
                TextColumn::make('date_mouvement')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('salleOrigine.id')
                    ->searchable(),
                TextColumn::make('salleDestination.id')
                    ->searchable(),
                TextColumn::make('place_numero')
                    ->searchable(),
                TextColumn::make('motif')
                    ->searchable(),
                TextColumn::make('nb_animaux')
                    ->numeric()
                    ->sortable(),
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
