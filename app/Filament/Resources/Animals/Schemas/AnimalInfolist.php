<?php

namespace App\Filament\Resources\Animals\Schemas;

use App\Models\Animal;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AnimalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification')
                    ->description('Informations d\'identification de l\'animal')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('numero_identification')
                            ->label('Numéro d\'identification'),
                        TextEntry::make('type_animal')
                            ->label('Type'),
                        TextEntry::make('race.nom')
                            ->label('Race'),
                        TextEntry::make('statut_actuel')
                            ->label('Statut'),
                    ]),

                Section::make('Dates clés')
                    ->description('Dates importantes de l\'animal')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('date_naissance')
                            ->label('Date de naissance')
                            ->date(),
                        TextEntry::make('date_entree')
                            ->label('Date d\'entrée')
                            ->date(),
                    ]),

                Section::make('Origine & Provenance')
                    ->description('Informations sur l\'origine de l\'animal')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('origine')
                            ->label('Origine'),
                        TextEntry::make('provenance')
                            ->label('Provenance')
                            ->placeholder('-'),
                    ]),

                Section::make('Filiation')
                    ->description('Parents de l\'animal')
                    ->icon(Heroicon::OutlinedUsers)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('numero_mere')
                            ->label('Numéro mère')
                            ->placeholder('-'),
                        TextEntry::make('numero_pere')
                            ->label('Numéro père')
                            ->placeholder('-'),
                    ]),

                Section::make('Réforme')
                    ->description('Informations de réforme')
                    ->icon(Heroicon::OutlinedArchiveBoxXMark)
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('date_reforme')
                            ->label('Date de réforme')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('motif_reforme')
                            ->label('Motif de réforme')
                            ->placeholder('-'),
                    ]),

                Section::make('Notes')
                    ->description('Informations complémentaires')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('notes')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Métadonnées')
                    ->description('Informations système')
                    ->icon(Heroicon::OutlinedClock)
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Modifié le')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->label('Supprimé le')
                            ->dateTime()
                            ->visible(fn (Animal $record): bool => $record->trashed()),
                    ]),
            ]);
    }
}
