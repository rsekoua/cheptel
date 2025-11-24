<?php

namespace App\Filament\Resources\PreparationAliments\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PreparationAlimentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nom')
                            ->label('Nom')
                            ->placeholder('Sans nom'),

                        TextEntry::make('date_preparation')
                            ->label('Date de préparation')
                            ->date('d/m/Y'),

                        TextEntry::make('user.name')
                            ->label('Créé par'),

                        TextEntry::make('notes')
                            ->label('Notes')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->notes !== null),
                    ]),

                Section::make('Résumé de la préparation')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('quantite_totale')
                            ->label('Quantité totale')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2)
                            ->weight('bold'),

                        TextEntry::make('cout_total')
                            ->label('Coût total')
                            ->suffix(' FCFA')
                            ->numeric(decimalPlaces: 0)
                            ->weight('bold'),
                    ]),

                Section::make('Composition')
                    ->schema([
                        RepeatableEntry::make('details')
                            ->label('Aliments utilisés')
                            ->schema([
                                TextEntry::make('aliment.nom')
                                    ->label('Aliment'),

                                TextEntry::make('quantite_kg')
                                    ->label('Quantité')
                                    ->suffix(' kg')
                                    ->numeric(decimalPlaces: 2),

                                TextEntry::make('cout_unitaire_kg')
                                    ->label('Coût unitaire')
                                    ->suffix(' FCFA/kg')
                                    ->numeric(decimalPlaces: 2),

                                TextEntry::make('valeur')
                                    ->label('Valeur')
                                    ->suffix(' FCFA')
                                    ->numeric(decimalPlaces: 0)
                                    ->weight('bold'),
                            ])
                            ->columns(4),
                    ]),

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
