<?php

namespace App\Filament\Resources\Salles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SallesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('typeSalle.nom')
                    ->searchable(),
                TextColumn::make('nom')
                    ->searchable(),
                TextColumn::make('capacite')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('statut')
                    ->searchable(),
                TextColumn::make('date_debut_vide_sanitaire')
                    ->date()
                    ->sortable(),
                TextColumn::make('duree_vide_sanitaire_jours')
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
