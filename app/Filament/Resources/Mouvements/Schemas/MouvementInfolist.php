<?php

namespace App\Filament\Resources\Mouvements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MouvementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type_cible'),
                TextEntry::make('animal.id')
                    ->label('Animal')
                    ->placeholder('-'),
                TextEntry::make('lot.id')
                    ->label('Lot')
                    ->placeholder('-'),
                TextEntry::make('date_mouvement')
                    ->dateTime(),
                TextEntry::make('salleOrigine.id')
                    ->label('Salle origine')
                    ->placeholder('-'),
                TextEntry::make('salleDestination.id')
                    ->label('Salle destination'),
                TextEntry::make('place_numero')
                    ->placeholder('-'),
                TextEntry::make('motif'),
                TextEntry::make('nb_animaux')
                    ->numeric(),
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
