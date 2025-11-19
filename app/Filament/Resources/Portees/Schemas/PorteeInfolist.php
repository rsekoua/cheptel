<?php

namespace App\Filament\Resources\Portees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PorteeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        TextEntry::make('cycleReproduction.numero_cycle')
                            ->label('Cycle de reproduction N°')
                            ->numeric()
                            ->badge()
                            ->color('info'),

                        TextEntry::make('animal.numero_identification')
                            ->label('Truie')
                            ->weight('bold')
                            ->size('lg'),
                        // ->description(fn ($record) => $record->animal?->type_animal),

                        TextEntry::make('date_mise_bas')
                            ->label('Date et heure de mise bas')
                            ->dateTime('d/m/Y H:i')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),

                Section::make('Données de mise bas')
                    ->icon(Heroicon::Cake)
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

                        TextEntry::make('nb_total')
                            ->label('Total vivants')
                            ->numeric()
                            ->suffix(' porcelets')
                            ->badge()
                            ->color('info')
                            ->size('lg'),

                        TextEntry::make('poids_moyen_naissance_g')
                            ->label('Poids moyen à la naissance')
                            ->numeric(decimalPlaces: 0)
                            ->suffix(' g')
                            ->placeholder('-')
                            ->helperText('Généralement entre 1200g et 1600g'),
                    ])
                    ->columns(3),

                Section::make('Données de sevrage')
                    ->icon(Heroicon::ArrowRightCircle)
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
                            ->color('success')
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
                            ->placeholder('-')
                            ->helperText('Généralement entre 6 et 8 kg'),
                    ])
                    ->columns(2),

                Section::make('Destination')
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->schema([
                        TextEntry::make('lotDestination.numero_lot')
                            ->label('Lot de destination')
                            ->placeholder('-')
                            ->badge()
                            ->color('info'),

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
                    ->icon(Heroicon::DocumentText)
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
