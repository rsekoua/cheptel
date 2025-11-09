<?php

namespace App\Filament\Resources\EvenementsAlimentations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EvenementsAlimentationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('lot.id')
                    ->label('Lot')
                    ->placeholder('-'),
                TextEntry::make('animal.id')
                    ->label('Animal')
                    ->placeholder('-'),
                TextEntry::make('date_debut')
                    ->date(),
                TextEntry::make('date_fin')
                    ->date(),
                TextEntry::make('planAlimentation.id')
                    ->label('Plan alimentation'),
                TextEntry::make('quantite_kg')
                    ->numeric(),
                TextEntry::make('nb_animaux')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('cout_total')
                    ->numeric()
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
