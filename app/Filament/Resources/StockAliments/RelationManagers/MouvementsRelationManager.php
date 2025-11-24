<?php

namespace App\Filament\Resources\StockAliments\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MouvementsRelationManager extends RelationManager
{
    protected static string $relationship = 'mouvements';

    protected static ?string $title = 'Historique des mouvements';

    protected static string|null|\BackedEnum $icon = 'heroicon-o-arrow-path';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date_mouvement')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type_mouvement')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'achat' => 'success',
                        'transfert_entree' => 'warning',
                        'transfert_sortie' => 'info',
                        'sortie_preparation' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'achat' => 'Achat',
                        'transfert_entree' => 'Transfert → Préparation',
                        'transfert_sortie' => 'Retour → Entrepôt',
                        'sortie_preparation' => 'Sortie',
                        default => $state,
                    }),

                TextColumn::make('type_stock')
                    ->label('Zone')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'entrepot' ? 'primary' : 'info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'entrepot' => 'Entrepôt',
                        'preparation' => 'Préparation',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('poids_kg')
                    ->label('Quantité')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 2)
                    ->weight('bold'),

                TextColumn::make('nombre_sacs')
                    ->label('Sacs')
                    ->suffix(' sacs')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('valeur_mouvement')
                    ->label('Valeur')
                    ->suffix(' FCFA')
                    ->numeric(decimalPlaces: 0)
                    ->placeholder('-'),

                TextColumn::make('reference')
                    ->label('Référence')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Créé par')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type_mouvement')
                    ->label('Type de mouvement')
                    ->options([
                        'achat' => 'Achat',
                        'transfert_entree' => 'Transfert → Préparation',
                        'transfert_sortie' => 'Retour → Entrepôt',
                        'sortie_preparation' => 'Sortie',
                    ]),

                SelectFilter::make('type_stock')
                    ->label('Zone')
                    ->options([
                        'entrepot' => 'Entrepôt',
                        'preparation' => 'Préparation',
                    ]),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultSort('date_mouvement', 'desc')
            ->emptyStateHeading('Aucun mouvement')
            ->emptyStateDescription('Il n\'y a pas encore de mouvements pour cet aliment.')
            ->emptyStateIcon('heroicon-o-arrow-path');
    }
}
