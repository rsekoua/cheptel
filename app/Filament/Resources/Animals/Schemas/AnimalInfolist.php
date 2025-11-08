<?php

namespace App\Filament\Resources\Animals\Schemas;

use App\Models\Animal;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AnimalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('numero_identification'),
                TextEntry::make('type_animal'),
                TextEntry::make('race.id')
                    ->label('Race'),
                TextEntry::make('sexe'),
                TextEntry::make('date_naissance')
                    ->date(),
                TextEntry::make('date_entree')
                    ->date(),
                TextEntry::make('origine'),
                TextEntry::make('numero_mere')
                    ->placeholder('-'),
                TextEntry::make('numero_pere')
                    ->placeholder('-'),
                TextEntry::make('statut_actuel'),
                TextEntry::make('salle.id')
                    ->label('Salle')
                    ->placeholder('-'),
                TextEntry::make('place_numero')
                    ->placeholder('-'),
                TextEntry::make('poids_actuel_kg')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('date_derniere_pesee')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('planAlimentation.id')
                    ->label('Plan alimentation')
                    ->placeholder('-'),
                TextEntry::make('bande')
                    ->placeholder('-'),
                TextEntry::make('date_reforme')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('motif_reforme')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Animal $record): bool => $record->trashed()),
            ]);
    }
}
