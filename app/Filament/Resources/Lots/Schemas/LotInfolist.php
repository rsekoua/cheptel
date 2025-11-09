<?php

namespace App\Filament\Resources\Lots\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LotInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextEntry::make('numero_lot')
                            ->label('Numéro du lot')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('type_lot')
                            ->label('Type de lot')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'post_sevrage' => 'info',
                                'engraissement' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'post_sevrage' => 'Post-sevrage',
                                'engraissement' => 'Engraissement',
                                default => $state,
                            }),

                        TextEntry::make('statut_lot')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'actif' => 'success',
                                'termine' => 'gray',
                                'vide' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'actif' => 'Actif',
                                'termine' => 'Terminé',
                                'vide' => 'Vide',
                                default => $state,
                            }),

                        TextEntry::make('date_creation')
                            ->label('Date de création')
                            ->date('d/m/Y'),
                    ])
                    ->columns(2),

                Section::make('Effectif et poids au départ')
                    ->schema([
                        TextEntry::make('nb_animaux_depart')
                            ->label('Nombre d\'animaux au départ')
                            ->numeric()
                            ->suffix(' animaux'),

                        TextEntry::make('poids_total_depart_kg')
                            ->label('Poids total au départ')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->placeholder('-'),

                        TextEntry::make('poids_moyen_depart_kg')
                            ->label('Poids moyen au départ')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Effectif et poids actuels')
                    ->schema([
                        TextEntry::make('nb_animaux_actuel')
                            ->label('Nombre d\'animaux actuel')
                            ->numeric()
                            ->suffix(' animaux'),

                        TextEntry::make('poids_total_actuel_kg')
                            ->label('Poids total actuel')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->placeholder('-'),

                        TextEntry::make('poids_moyen_actuel_kg')
                            ->label('Poids moyen actuel')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->placeholder('-'),

                        TextEntry::make('date_derniere_pesee')
                            ->label('Date de dernière pesée')
                            ->date('d/m/Y')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Indicateurs de performance')
                    ->schema([
                        TextEntry::make('taux_mortalite')
                            ->label('Taux de mortalité')
                            ->state(fn ($record) => $record->taux_mortalite)
                            ->badge()
                            ->color(fn ($state): string => match (true) {
                                $state === null => 'gray',
                                $state < 3 => 'success',
                                $state < 5 => 'warning',
                                default => 'danger',
                            })
                            ->formatStateUsing(fn ($state): string => $state !== null ? number_format($state, 2).' %' : '-'),

                        TextEntry::make('gmq')
                            ->label('GMQ (Gain Moyen Quotidien)')
                            ->state(fn ($record) => $record->gmq)
                            ->badge()
                            ->color(fn ($state): string => match (true) {
                                $state === null => 'gray',
                                $state >= 700 => 'success',
                                $state >= 600 => 'warning',
                                default => 'danger',
                            })
                            ->formatStateUsing(fn ($state): string => $state !== null ? $state.' g/jour' : '-'),
                    ])
                    ->columns(2),

                Section::make('Localisation et alimentation')
                    ->schema([
                        TextEntry::make('salle.nom')
                            ->label('Salle')
                            ->placeholder('-'),

                        TextEntry::make('planAlimentation.nom')
                            ->label('Plan d\'alimentation')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Sortie du lot')
                    ->schema([
                        TextEntry::make('date_sortie')
                            ->label('Date de sortie')
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('nb_animaux_sortie')
                            ->label('Nombre d\'animaux sortis')
                            ->numeric()
                            ->suffix(' animaux')
                            ->placeholder('-'),

                        TextEntry::make('poids_total_sortie_kg')
                            ->label('Poids total à la sortie')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->placeholder('-'),

                        TextEntry::make('poids_moyen_sortie_kg')
                            ->label('Poids moyen à la sortie')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->placeholder('-'),

                        TextEntry::make('prix_vente_total')
                            ->label('Prix de vente total')
                            ->money('EUR')
                            ->placeholder('-'),

                        TextEntry::make('destination_sortie')
                            ->label('Destination de sortie')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Section::make('Notes et horodatage')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Modifié le')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
