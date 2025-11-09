<?php

namespace App\Filament\Resources\Taches\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TacheInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('titre'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('type_tache'),
                TextEntry::make('priorite'),
                TextEntry::make('type_cible'),
                TextEntry::make('animal.id')
                    ->label('Animal')
                    ->placeholder('-'),
                TextEntry::make('lot.id')
                    ->label('Lot')
                    ->placeholder('-'),
                TextEntry::make('portee.id')
                    ->label('Portee')
                    ->placeholder('-'),
                TextEntry::make('salle.id')
                    ->label('Salle')
                    ->placeholder('-'),
                TextEntry::make('date_echeance')
                    ->date(),
                TextEntry::make('date_debut_periode')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('statut'),
                TextEntry::make('date_realisation')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('utilisateurAssigne.name')
                    ->label('Utilisateur assigne')
                    ->placeholder('-'),
                TextEntry::make('utilisateurRealisation.name')
                    ->label('Utilisateur realisation')
                    ->placeholder('-'),
                IconEntry::make('generee_automatiquement')
                    ->boolean(),
                TextEntry::make('evenement_lie_type')
                    ->placeholder('-'),
                TextEntry::make('evenement_lie_id')
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
