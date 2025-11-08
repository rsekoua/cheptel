<?php

namespace App\Filament\Resources\Salles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SalleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('typeSalle.id')
                    ->label('Type salle'),
                TextEntry::make('nom'),
                TextEntry::make('capacite')
                    ->numeric(),
                TextEntry::make('statut'),
                TextEntry::make('date_debut_vide_sanitaire')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('duree_vide_sanitaire_jours')
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
