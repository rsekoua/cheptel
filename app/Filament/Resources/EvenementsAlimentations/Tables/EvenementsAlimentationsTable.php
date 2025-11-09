<?php

namespace App\Filament\Resources\EvenementsAlimentations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvenementsAlimentationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lot.id')
                    ->searchable(),
                TextColumn::make('animal.id')
                    ->searchable(),
                TextColumn::make('date_debut')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_fin')
                    ->date()
                    ->sortable(),
                TextColumn::make('planAlimentation.id')
                    ->searchable(),
                TextColumn::make('quantite_kg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nb_animaux')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cout_total')
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
