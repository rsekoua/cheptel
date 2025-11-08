<?php

namespace App\Filament\Resources\Animals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AnimalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_identification')
                    ->searchable(),
                TextColumn::make('type_animal')
                    ->searchable(),
                TextColumn::make('race.id')
                    ->searchable(),
                TextColumn::make('sexe')
                    ->searchable(),
                TextColumn::make('date_naissance')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_entree')
                    ->date()
                    ->sortable(),
                TextColumn::make('origine')
                    ->searchable(),
                TextColumn::make('numero_mere')
                    ->searchable(),
                TextColumn::make('numero_pere')
                    ->searchable(),
                TextColumn::make('statut_actuel')
                    ->searchable(),
                TextColumn::make('salle.id')
                    ->searchable(),
                TextColumn::make('place_numero')
                    ->searchable(),
                TextColumn::make('poids_actuel_kg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date_derniere_pesee')
                    ->date()
                    ->sortable(),
                TextColumn::make('planAlimentation.id')
                    ->searchable(),
                TextColumn::make('bande')
                    ->searchable(),
                TextColumn::make('date_reforme')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
