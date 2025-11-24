<?php

namespace App\Filament\Resources\StockAliments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockAlimentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations sur l\'aliment')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nom')
                            ->label('Nom de l\'aliment')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('actif')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Actif' : 'Inactif'),

                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->placeholder('Aucune description'),

                        TextEntry::make('poids_sac_standard')
                            ->label('Poids standard d\'un sac')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2),

                        TextEntry::make('seuil_alerte')
                            ->label('Seuil d\'alerte')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2),
                    ]),

                Section::make('Stock entrepôt')
                    ->description('Stock disponible dans l\'entrepôt principal')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('stockEntrepot.poids_kg_disponible')
                            ->label('Poids disponible')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2)
                            ->size('lg')
                            ->weight('bold')
                            ->placeholder('0.00 kg')
                            ->color(function ($record) {
                                $stock = $record->stockEntrepot?->poids_kg_disponible ?? 0;

                                return $stock < $record->seuil_alerte ? 'danger' : 'success';
                            }),

                        TextEntry::make('stockEntrepot.cout_moyen_kg')
                            ->label('Coût moyen par kg')
                            ->suffix(' FCFA/kg')
                            ->numeric(decimalPlaces: 2)
                            ->placeholder('-'),

                        TextEntry::make('stockEntrepot.valeur_stock')
                            ->label('Valeur du stock')
                            ->suffix(' FCFA')
                            ->numeric(decimalPlaces: 0)
                            ->size('lg')
                            ->weight('bold')
                            ->placeholder('0 FCFA'),
                    ]),

                Section::make('Stock préparation')
                    ->description('Stock disponible en zone de préparation')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('stockPreparation.poids_kg_disponible')
                            ->label('Poids disponible')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2)
                            ->size('lg')
                            ->weight('bold')
                            ->placeholder('0.00 kg')
                            ->color('primary'),

                        TextEntry::make('stockPreparation.cout_moyen_kg')
                            ->label('Coût moyen par kg')
                            ->suffix(' FCFA/kg')
                            ->numeric(decimalPlaces: 2)
                            ->placeholder('-'),

                        TextEntry::make('stockPreparation.valeur_stock')
                            ->label('Valeur du stock')
                            ->suffix(' FCFA')
                            ->numeric(decimalPlaces: 0)
                            ->size('lg')
                            ->weight('bold')
                            ->placeholder('0 FCFA'),
                    ]),

                Section::make('Totaux')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('stock_total_poids')
                            ->label('Poids total (entrepôt + préparation)')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2)
                            ->size('lg')
                            ->weight('bold')
                            ->state(function ($record) {
                                $entrepot = $record->stockEntrepot?->poids_kg_disponible ?? 0;
                                $preparation = $record->stockPreparation?->poids_kg_disponible ?? 0;

                                return $entrepot + $preparation;
                            }),

                        TextEntry::make('valeur_totale')
                            ->label('Valeur totale')
                            ->suffix(' FCFA')
                            ->numeric(decimalPlaces: 0)
                            ->size('lg')
                            ->weight('bold')
                            ->color('success')
                            ->state(function ($record) {
                                $valeurEntrepot = $record->stockEntrepot?->valeur_stock ?? 0;
                                $valeurPreparation = $record->stockPreparation?->valeur_stock ?? 0;

                                return $valeurEntrepot + $valeurPreparation;
                            }),
                    ]),
            ]);
    }
}
