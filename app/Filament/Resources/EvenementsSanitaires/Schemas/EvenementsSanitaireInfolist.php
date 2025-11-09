<?php

namespace App\Filament\Resources\EvenementsSanitaires\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EvenementsSanitaireInfolist
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
                TextEntry::make('date_evenement')
                    ->dateTime(),
                TextEntry::make('type_evenement'),
                TextEntry::make('produitSanitaire.id')
                    ->label('Produit sanitaire')
                    ->placeholder('-'),
                TextEntry::make('dose_administree')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('nb_animaux_traites')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('intervenant')
                    ->placeholder('-'),
                TextEntry::make('motif')
                    ->placeholder('-')
                    ->columnSpanFull(),
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
