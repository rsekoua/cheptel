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
                Section::make('Informations')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('aliment.nom')
                            ->label('Aliment')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('type_stock')
                            ->label('Zone de stock')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'entrepot' ? 'primary' : 'info')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'entrepot' => 'Entrepôt',
                                'preparation' => 'Préparation',
                                default => $state,
                            }),
                    ]),

                Section::make('Quantités disponibles')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nombre_sacs_disponibles')
                            ->label('Nombre de sacs')
                            ->suffix(' sacs')
                            ->numeric(decimalPlaces: 2)
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('poids_kg_disponible')
                            ->label('Poids total')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2)
                            ->size('lg')
                            ->weight('bold')
                            ->color(function ($record) {
                                if ($record->type_stock === 'entrepot' && $record->aliment) {
                                    return $record->poids_kg_disponible < $record->aliment->seuil_alerte ? 'danger' : 'success';
                                }

                                return 'primary';
                            }),
                    ]),

                Section::make('Valeur du stock')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('cout_moyen_kg')
                            ->label('Coût moyen par kg')
                            ->suffix(' FCFA/kg')
                            ->numeric(decimalPlaces: 2),

                        TextEntry::make('valeur_stock')
                            ->label('Valeur totale du stock')
                            ->suffix(' FCFA')
                            ->numeric(decimalPlaces: 0)
                            ->size('lg')
                            ->weight('bold'),
                    ]),

                Section::make('Dernière mise à jour')
                    ->schema([
                        TextEntry::make('derniere_maj')
                            ->label('Dernière modification')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Jamais modifié'),
                    ]),
            ]);
    }
}
