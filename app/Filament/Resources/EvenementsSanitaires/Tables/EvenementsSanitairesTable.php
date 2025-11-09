<?php

namespace App\Filament\Resources\EvenementsSanitaires\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvenementsSanitairesTable
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
                TextColumn::make('date_evenement')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('type_evenement')
                    ->searchable(),
                TextColumn::make('produitSanitaire.id')
                    ->searchable(),
                TextColumn::make('dose_administree')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nb_animaux_traites')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('intervenant')
                    ->searchable(),
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
