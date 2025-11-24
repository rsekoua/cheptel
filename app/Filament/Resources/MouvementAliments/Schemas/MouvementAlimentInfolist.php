<?php

namespace App\Filament\Resources\MouvementAliments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MouvementAlimentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du mouvement')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('type_mouvement')
                            ->label('Type de mouvement')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'achat' => 'success',
                                'transfert_entree' => 'info',
                                'transfert_sortie' => 'warning',
                                'sortie_preparation' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'achat' => 'Achat',
                                'transfert_entree' => 'Transfert vers préparation',
                                'transfert_sortie' => 'Transfert vers entrepôt',
                                'sortie_preparation' => 'Sortie pour utilisation',
                                default => $state,
                            }),

                        TextEntry::make('type_stock')
                            ->label('Zone de stock')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'entrepot' => 'Entrepôt',
                                'preparation' => 'Préparation',
                                default => $state,
                            }),

                        TextEntry::make('aliment.nom')
                            ->label('Aliment'),

                        TextEntry::make('date_mouvement')
                            ->label('Date')
                            ->date('d/m/Y'),

                        TextEntry::make('user.name')
                            ->label('Créé par'),
                    ]),

                Section::make('Quantités et coûts')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nombre_sacs')
                            ->label('Nombre de sacs')
                            ->suffix(' sacs')
                            ->numeric(decimalPlaces: 2)
                            ->visible(fn ($record) => $record->nombre_sacs > 0),

                        TextEntry::make('poids_kg')
                            ->label('Poids')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2),

                        TextEntry::make('prix_unitaire_sac')
                            ->label('Prix unitaire par sac')
                            ->suffix(' FCFA')
                            ->numeric(decimalPlaces: 2)
                            ->visible(fn ($record) => $record->prix_unitaire_sac !== null),

                        TextEntry::make('cout_total')
                            ->label('Coût total')
                            ->suffix(' FCFA')
                            ->numeric(decimalPlaces: 2)
                            ->visible(fn ($record) => $record->cout_total !== null),

                        TextEntry::make('cout_unitaire_kg')
                            ->label('Coût par kg')
                            ->suffix(' FCFA/kg')
                            ->numeric(decimalPlaces: 2),

                        TextEntry::make('valeur_mouvement')
                            ->label('Valeur du mouvement')
                            ->suffix(' FCFA')
                            ->numeric(decimalPlaces: 2)
                            ->weight('bold'),
                    ]),

                Section::make('Informations complémentaires')
                    ->schema([
                        TextEntry::make('reference')
                            ->label('Référence')
                            ->visible(fn ($record) => $record->reference !== null),

                        TextEntry::make('notes')
                            ->label('Notes')
                            ->visible(fn ($record) => $record->notes !== null),
                    ])
                    ->collapsible(),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Modifié le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
