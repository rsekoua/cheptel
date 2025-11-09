<?php

namespace App\Filament\Resources\Saillies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SaillieInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cycleReproduction.id')
                    ->label('Cycle reproduction'),
                TextEntry::make('date_heure')
                    ->dateTime(),
                TextEntry::make('type'),
                TextEntry::make('verrat.id')
                    ->label('Verrat')
                    ->placeholder('-'),
                TextEntry::make('semence_lot_numero')
                    ->placeholder('-'),
                TextEntry::make('intervenant')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
