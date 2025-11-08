<?php

namespace App\Filament\Resources\PlanAlimentations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PlanAlimentationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nom'),
                TextEntry::make('type_animal'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('energie_mcal_jour')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('proteine_pourcent')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('ration_kg_jour')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('a_volonte')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
