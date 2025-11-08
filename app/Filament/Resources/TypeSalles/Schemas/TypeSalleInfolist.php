<?php

namespace App\Filament\Resources\TypeSalles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TypeSalleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nom'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('capacite_type'),
                TextEntry::make('temperature_optimale')
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
