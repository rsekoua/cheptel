<?php

namespace App\Filament\Resources\Taches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TachesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')
                    ->searchable(),
                TextColumn::make('type_tache')
                    ->searchable(),
                TextColumn::make('priorite')
                    ->searchable(),
                TextColumn::make('type_cible')
                    ->searchable(),
                TextColumn::make('animal.id')
                    ->searchable(),
                TextColumn::make('lot.id')
                    ->searchable(),
                TextColumn::make('portee.id')
                    ->searchable(),
                TextColumn::make('salle.id')
                    ->searchable(),
                TextColumn::make('date_echeance')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_debut_periode')
                    ->date()
                    ->sortable(),
                TextColumn::make('statut')
                    ->searchable(),
                TextColumn::make('date_realisation')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('utilisateurAssigne.name')
                    ->searchable(),
                TextColumn::make('utilisateurRealisation.name')
                    ->searchable(),
                IconColumn::make('generee_automatiquement')
                    ->boolean(),
                TextColumn::make('evenement_lie_type')
                    ->searchable(),
                TextColumn::make('evenement_lie_id')
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
