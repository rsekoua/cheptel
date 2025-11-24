<?php

namespace App\Filament\Resources\Aliments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AlimentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nom')
                            ->label('Nom'),

                        IconEntry::make('actif')
                            ->label('Statut')
                            ->boolean(),

                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),

                Section::make('Configuration')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('poids_sac_standard')
                            ->label('Poids sac standard')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2),

                        TextEntry::make('seuil_alerte')
                            ->label('Seuil d\'alerte')
                            ->suffix(' kg')
                            ->numeric(decimalPlaces: 2),
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
