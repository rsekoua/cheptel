<?php

namespace App\Filament\Resources\Races\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RaceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nom'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('type'),
                TextEntry::make('gmq_moyen')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('poids_adulte_moyen')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
