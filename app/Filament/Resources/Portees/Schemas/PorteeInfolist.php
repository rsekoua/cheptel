<?php

namespace App\Filament\Resources\Portees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PorteeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextEntry::make('animal.numero_identification')
                            ->label('Truie/Cochette')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('animal.type_animal')
                            ->label('Type')
                            ->badge(),

                        TextEntry::make('cycleReproduction.numero_cycle')
                            ->label('Cycle de reproduction N°')
                            ->numeric(),

                        TextEntry::make('date_mise_bas')
                            ->label('Date et heure de mise-bas')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),

                Section::make('Données de mise-bas')
                    ->schema([
                        TextEntry::make('nb_nes_vifs')
                            ->label('Nés vivants')
                            ->numeric()
                            ->suffix(' porcelets')
                            ->color('success')
                            ->badge(),

                        TextEntry::make('nb_mort_nes')
                            ->label('Mort-nés')
                            ->numeric()
                            ->suffix(' porcelets')
                            ->color(fn ($state) => $state > 0 ? 'danger' : 'gray')
                            ->badge(),

                        TextEntry::make('nb_momifies')
                            ->label('Momifiés')
                            ->numeric()
                            ->suffix(' porcelets')
                            ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                            ->badge(),

                        TextEntry::make('nb_total')
                            ->label('Nombre total')
                            ->numeric()
                            ->suffix(' porcelets')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('poids_moyen_naissance_g')
                            ->label('Poids moyen à la naissance')
                            ->numeric(decimalPlaces: 0)
                            ->suffix(' g')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Données de sevrage')
                    ->schema([
                        TextEntry::make('date_sevrage')
                            ->label('Date de sevrage')
                            ->date('d/m/Y')
                            ->placeholder('Non sevré')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'warning'),

                        TextEntry::make('nb_sevres')
                            ->label('Nombre de sevrés')
                            ->numeric()
                            ->suffix(' porcelets')
                            ->placeholder('-')
                            ->color('info')
                            ->badge(),

                        TextEntry::make('poids_total_sevrage_kg')
                            ->label('Poids total au sevrage')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->placeholder('-'),

                        TextEntry::make('poids_moyen_sevrage_kg')
                            ->label('Poids moyen au sevrage')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Indicateurs de performance')
                    ->schema([
                        TextEntry::make('taux_mortalite_maternite')
                            ->label('Taux de mortalité en maternité')
                            ->state(fn ($record) => $record->taux_mortalite_maternite)
                            ->badge()
                            ->color(fn ($state): string => match (true) {
                                $state === null => 'gray',
                                $state < 10 => 'success',
                                $state < 15 => 'warning',
                                default => 'danger',
                            })
                            ->formatStateUsing(fn ($state): string => $state !== null ? number_format($state, 2).' %' : '-')
                            ->helperText('Pourcentage de mortalité entre la naissance et le sevrage'),
                    ])
                    ->columns(1),

                Section::make('Destination')
                    ->schema([
                        TextEntry::make('lotDestination.numero_lot')
                            ->label('Lot de destination')
                            ->placeholder('-'),

                        TextEntry::make('lotDestination.type_lot')
                            ->label('Type de lot')
                            ->placeholder('-')
                            ->badge()
                            ->color(fn ($state): string => match ($state) {
                                'post_sevrage' => 'info',
                                'engraissement' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state): string => match ($state) {
                                'post_sevrage' => 'Post-sevrage',
                                'engraissement' => 'Engraissement',
                                default => $state ?? '-',
                            }),
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
