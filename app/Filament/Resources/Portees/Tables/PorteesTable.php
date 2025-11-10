<?php

namespace App\Filament\Resources\Portees\Tables;

use App\Filament\Resources\Portees\Actions\SevrerPorteesAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PorteesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('animal.numero_identification')
                    ->label('Truie/Cochette')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->animal?->type_animal),

                TextColumn::make('date_mise_bas')
                    ->label('Mise-bas')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('nb_nes_vifs')
                    ->label('Nés vivants')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->suffix(' porcelets')
                    ->color('success'),

                TextColumn::make('nb_mort_nes')
                    ->label('Mort-nés')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->suffix(' porcelets')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray')
                    ->toggleable(),

                TextColumn::make('nb_momifies')
                    ->label('Momifiés')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->suffix(' porcelets')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                    ->toggleable(),

                TextColumn::make('nb_total')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->suffix(' porcelets'),

                TextColumn::make('poids_moyen_naissance_g')
                    ->label('Poids moyen nais.')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' g')
                    ->sortable()
                    ->alignCenter()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('date_sevrage')
                    ->label('Sevrage')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Non sevré')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning'),

                TextColumn::make('nb_sevres')
                    ->label('Sevrés')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->suffix(' porcelets')
                    ->placeholder('-')
                    ->color('info'),

                TextColumn::make('poids_moyen_sevrage_kg')
                    ->label('Poids moyen sev.')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable()
                    ->alignCenter()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('taux_natalite_maternite')
                    ->label('Natalité')
                    ->state(fn ($record) => $record->taux_natalite_maternite)
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state > 90 => 'success',
                        $state > 85 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => $state !== null ? number_format($state, 2).' %' : '-')
                    ->tooltip('Taux de mortalité en maternité (de la naissance au sevrage)')
                    ->alignCenter()
                    ->sortable(),

//                TextColumn::make('taux_mortalite_maternite')
//                    ->label('Mortalité')
//                    ->state(fn ($record) => $record->taux_mortalite_maternite)
//                    ->badge()
//                    ->color(fn ($state): string => match (true) {
//                        $state === null => 'gray',
//                        $state < 10 => 'success',
//                        $state < 15 => 'warning',
//                        default => 'danger',
//                    })
//                    ->formatStateUsing(fn ($state): string => $state !== null ? number_format($state, 2).' %' : '-')
//                    ->tooltip('Taux de mortalité en maternité (de la naissance au sevrage)')
//                    ->alignCenter()
//                    ->sortable(),

                TextColumn::make('lotDestination.numero_lot')
                    ->label('Lot destination')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('cycleReproduction.numero_cycle')
                    ->label('Cycle N°')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('sevrees')
                    ->label('Sevrées')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('date_sevrage')),

                Filter::make('non_sevrees')
                    ->label('Non sevrées')
                    ->query(fn (Builder $query): Builder => $query->whereNull('date_sevrage')),

                SelectFilter::make('animal_id')
                    ->label('Truie/Cochette')
                    ->relationship('animal', 'numero_identification')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('lot_destination_id')
                    ->label('Lot destination')
                    ->relationship('lotDestination', 'numero_lot')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    SevrerPorteesAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_mise_bas', 'desc');
    }
}
