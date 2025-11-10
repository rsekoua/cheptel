<?php

namespace App\Filament\Resources\CycleReproductions\Schemas;

// use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class CycleReproductionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // En-tête avec informations clés
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('animal.numero_identification')
                                    ->label('Animal')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-identification')
                                    ->url(fn ($record) => $record->animal ? \App\Filament\Resources\Animals\AnimalResource::getUrl('edit', ['record' => $record->animal_id]) : null)
                                    ->color('primary'),

                                TextEntry::make('numero_cycle')
                                    ->label('Cycle N°')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-arrow-path')
                                    ->formatStateUsing(fn ($state) => "#{$state}"),

                                TextEntry::make('statut_cycle')
                                    ->label('Statut')
                                    ->badge()
                                    ->size(TextSize::Large)
                                    ->color(fn (string $state): string => match ($state) {
                                        'en_cours' => 'info',
                                        'termine_succes' => 'success',
                                        'termine_echec' => 'danger',
                                        'avorte' => 'warning',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'en_cours' => 'heroicon-o-clock',
                                        'termine_succes' => 'heroicon-o-check-circle',
                                        'termine_echec' => 'heroicon-o-x-circle',
                                        'avorte' => 'heroicon-o-exclamation-triangle',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'en_cours' => 'En cours',
                                        'termine_succes' => 'Terminé avec succès',
                                        'termine_echec' => 'Terminé en échec',
                                        'avorte' => 'Avorté',
                                        default => $state,
                                    }),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('date_debut')
                                    ->label('Début du cycle')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color('gray'),

                                TextEntry::make('duree_cycle')
                                    ->label('Durée du cycle')
                                    ->icon('heroicon-o-clock')
                                    ->color('gray')
                                    ->state(function ($record) {
                                        if (! $record->date_debut) {
                                            return '-';
                                        }

                                        $debut = \Carbon\Carbon::parse($record->date_debut);
                                        $fin = $record->date_mise_bas_reelle
                                            ? \Carbon\Carbon::parse($record->date_mise_bas_reelle)
                                            : now();

                                        return $debut->diffInDays($fin).' jours';
                                    }),

                                TextEntry::make('animal.type_animal')
                                    ->label('Type d\'animal')
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'truie' => 'Truie',
                                        'cochette' => 'Cochette',
                                        default => $state ?? '-',
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),

                // Section Chaleurs
                Section::make('🌡️ Chaleurs')
                    ->description('Observation des chaleurs de l\'animal')
                    ->schema([
                        TextEntry::make('date_chaleurs')
                            ->label('Date et heure des chaleurs')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-calendar-days')
                            ->placeholder('Non enregistrée')
                            ->color('warning'),
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => $record->date_chaleurs),

                // Section Saillies/Inséminations
                Section::make('💉 Saillies / Inséminations')
                    ->description('Historique complet des saillies et inséminations')
                    ->schema([
                        RepeatableEntry::make('saillies')
                            ->label('')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('date_heure')
                                            ->label('Date et heure')
                                            ->dateTime('d/m/Y H:i')
                                            ->icon('heroicon-o-calendar')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('type')
                                            ->label('Type')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'IA' => 'info',
                                                'MN' => 'success',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'IA' => 'Insémination Artificielle',
                                                'MN' => 'Monte Naturelle',
                                                default => $state,
                                            }),

                                        TextEntry::make('verrat.numero_identification')
                                            ->label('Verrat')
                                            ->icon('heroicon-o-identification')
                                            ->placeholder('-')
                                            ->visible(fn ($record) => $record->type === 'MN'),

                                        TextEntry::make('semence_lot_numero')
                                            ->label('N° lot semence')
                                            ->icon('heroicon-o-beaker')
                                            ->placeholder('-')
                                            ->visible(fn ($record) => $record->type === 'IA'),

                                        TextEntry::make('intervenant')
                                            ->label('Intervenant')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('-'),

                                        TextEntry::make('notes')
                                            ->label('Notes')
                                            ->columnSpan(3)
                                            ->placeholder('-')
                                            ->visible(fn ($record) => $record->notes),
                                    ]),
                            ])
                            ->contained(false),

                        // Résumé des saillies
                        Fieldset::make('Résumé')
                            ->schema([
                                TextEntry::make('nombre_saillies')
                                    ->label('Nombre total')
                                    ->icon('heroicon-o-hashtag')
                                    ->state(fn ($record) => $record->saillies()->count().' saillie(s)'),

                                TextEntry::make('date_premiere_saillie')
                                    ->label('Première saillie')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('-'),

                                TextEntry::make('type_saillie')
                                    ->label('Type principal')
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'IA' => 'Insémination Artificielle (IA)',
                                        'MN' => 'Monte Naturelle (MN)',
                                        default => '-',
                                    }),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => $record->saillies()->count() > 0),

                // Section Diagnostic
                Section::make('🔬 Diagnostic de gestation')
                    ->description('Résultat du diagnostic de gestation')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('date_diagnostic')
                                    ->label('Date du diagnostic')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('Non effectué'),

                                TextEntry::make('resultat_diagnostic')
                                    ->label('Résultat')
                                    ->badge()
                                    ->size(TextSize::Large)
                                    ->color(fn (string $state): string => match ($state) {
                                        'positif' => 'success',
                                        'negatif' => 'danger',
                                        'en_attente' => 'gray',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'positif' => 'heroicon-o-check-circle',
                                        'negatif' => 'heroicon-o-x-circle',
                                        'en_attente' => 'heroicon-o-clock',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'positif' => 'Positif (gestante)',
                                        'negatif' => 'Négatif (vide)',
                                        'en_attente' => 'En attente',
                                        default => $state,
                                    }),

                                TextEntry::make('jours_post_saillie')
                                    ->label('Jours après saillie')
                                    ->icon('heroicon-o-calculator')
                                    ->color('gray')
                                    ->state(function ($record) {
                                        if (! $record->date_premiere_saillie || ! $record->date_diagnostic) {
                                            return '-';
                                        }

                                        $saillie = \Carbon\Carbon::parse($record->date_premiere_saillie);
                                        $diagnostic = \Carbon\Carbon::parse($record->date_diagnostic);

                                        return $saillie->diffInDays($diagnostic).' jours';
                                    })
                                    ->visible(fn ($record) => $record->date_premiere_saillie && $record->date_diagnostic),
                            ]),
                    ])
                    ->collapsible(),

                // Section Mise-bas
                Section::make('🐷 Mise-bas')
                    ->description('Dates prévisionnelle et réelle de mise-bas')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('date_mise_bas_prevue')
                                    ->label('Date prévue')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar-days')
                                    ->color('info')
                                    ->placeholder('Non calculée'),

                                TextEntry::make('date_mise_bas_reelle')
                                    ->label('Date réelle')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color('success')
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('Non effectuée'),

                                TextEntry::make('ecart_mise_bas')
                                    ->label('Écart prévue/réelle')
                                    ->icon('heroicon-o-arrows-right-left')
                                    ->badge()
                                    ->color(fn ($state) => $state === '-' ? 'gray' : (abs((int) filter_var($state, FILTER_SANITIZE_NUMBER_INT)) <= 2 ? 'success' : 'warning'))
                                    ->state(function ($record) {
                                        if (! $record->date_mise_bas_prevue || ! $record->date_mise_bas_reelle) {
                                            return '-';
                                        }

                                        $prevue = \Carbon\Carbon::parse($record->date_mise_bas_prevue);
                                        $reelle = \Carbon\Carbon::parse($record->date_mise_bas_reelle);
                                        $diff = $prevue->diffInDays($reelle, false);

                                        if ($diff > 0) {
                                            return "+{$diff} jours (en retard)";
                                        } elseif ($diff < 0) {
                                            return abs($diff).' jours (en avance)';
                                        } else {
                                            return 'À la date prévue';
                                        }
                                    }),
                            ]),

                        TextEntry::make('duree_gestation')
                            ->label('Durée de gestation réelle')
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->color('primary')
                            ->state(function ($record) {
                                if (! $record->date_premiere_saillie || ! $record->date_mise_bas_reelle) {
                                    return null;
                                }

                                $saillie = \Carbon\Carbon::parse($record->date_premiere_saillie);
                                $miseBas = \Carbon\Carbon::parse($record->date_mise_bas_reelle);

                                return $saillie->diffInDays($miseBas).' jours';
                            })
                            ->visible(fn ($record) => $record->date_premiere_saillie && $record->date_mise_bas_reelle),

                        TextEntry::make('portee.numero_identification')
                            ->label('Portée associée')
                            ->icon('heroicon-o-identification')
                            ->url(fn ($record) => $record->portee ? \App\Filament\Resources\Portees\PorteeResource::getUrl('view', ['record' => $record->portee->id]) : null)
                            ->color('primary')
                            ->placeholder('Aucune portée enregistrée')
                            ->visible(fn ($record) => $record->portee),
                    ])
                    ->collapsible(),

                // Section Échec
                Section::make('❌ Informations sur l\'échec')
                    ->schema([
                        TextEntry::make('motif_echec')
                            ->label('Motif de l\'échec')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => in_array($record->statut_cycle, ['termine_echec', 'avorte'])),

                // Section Notes
                Section::make('📝 Notes complémentaires')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->columnSpanFull()
                            ->placeholder('Aucune note')
                            ->markdown(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => $record->notes),

                // Informations système
                Section::make('ℹ️ Informations système')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-plus-circle'),

                        TextEntry::make('updated_at')
                            ->label('Dernière modification')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
