<?php

namespace App\Filament\Resources\Pesees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PeseeInfolist
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
                TextEntry::make('date_pesee')
                    ->date(),
                TextEntry::make('poids_total_kg')
                    ->numeric(),
                TextEntry::make('nb_animaux_peses')
                    ->numeric(),
                TextEntry::make('poids_moyen_kg')
                    ->numeric(),
                TextEntry::make('methode'),
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
