<?php

namespace App\Filament\Resources\PreparationAliments\Schemas;

use App\Models\Aliment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PreparationAlimentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informations générales')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nom')
                            ->label('Nom de la préparation')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Nom optionnel pour identifier cette préparation')
                                    ->color('gray'),
                            ])),

                        DatePicker::make('date_preparation')
                            ->label('Date de préparation')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Date à laquelle la préparation a été effectuée')
                                    ->color('gray'),
                            ])),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::QuestionMarkCircle)
                                    ->tooltip('Notes ou informations complémentaires sur cette préparation')
                                    ->color('gray'),
                            ])),
                    ]),

                Section::make('Composition de la préparation')
                    ->description('Ajouter les aliments utilisés dans cette préparation')
                    ->schema([
                        Repeater::make('details')
                            ->relationship('details')
                            ->label('Aliments')
                            ->columns(2)
                            ->schema([
                                Select::make('aliment_id')
                                    ->label('Aliment')
                                    ->required()
                                    ->relationship(
                                        name: 'aliment',
                                        titleAttribute: 'nom',
                                        modifyQueryUsing: fn ($query) => $query
                                            ->whereHas('stockPreparation', function ($q) {
                                                $q->where('poids_kg_disponible', '>', 0);
                                            })
                                            ->where('actif', true)
                                            ->orderBy('nom')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $stockPreparation = $record->stockPreparation;
                                        $poidsDisponible = $stockPreparation?->poids_kg_disponible ?? 0;
                                        $coutMoyen = $stockPreparation?->cout_moyen_kg ?? 0;

                                        return "{$record->nom} ({$poidsDisponible} kg @ ".number_format($coutMoyen, 0, ',', ' ').' FCFA/kg)';
                                    })
                                    ->afterLabel(Schema::start([
                                        Icon::make(Heroicon::InformationCircle)
                                            ->tooltip('Seuls les aliments présents dans le stock de préparation sont disponibles. Le coût sera automatiquement calculé selon le coût moyen du stock.')
                                            ->color('info'),
                                    ])),

                                TextInput::make('quantite_kg')
                                    ->label('Quantité')
                                    ->required()
                                    ->numeric()
                                    ->suffix('kg')
                                    ->minValue(0.01)
                                    ->columnSpan(1)
                                    ->live(onBlur: true)
                                    ->helperText(function (Get $get) {
                                        $alimentId = $get('aliment_id');
                                        if (! $alimentId) {
                                            return 'Sélectionnez d\'abord un aliment';
                                        }

                                        $aliment = Aliment::with('stockPreparation')->find($alimentId);
                                        $stockDispo = $aliment?->stockPreparation?->poids_kg_disponible ?? 0;

                                        return "Stock disponible : {$stockDispo} kg";
                                    })
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, $value, $fail) use ($get) {
                                                $alimentId = $get('aliment_id');
                                                if (! $alimentId) {
                                                    return;
                                                }

                                                $aliment = Aliment::with('stockPreparation')->find($alimentId);
                                                $stockDispo = $aliment?->stockPreparation?->poids_kg_disponible ?? 0;

                                                if ($value > $stockDispo) {
                                                    $fail("La quantité demandée ({$value} kg) dépasse le stock disponible ({$stockDispo} kg).");
                                                }
                                            };
                                        },
                                    ]),
                            ])
                            ->addActionLabel('Ajouter un aliment')
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
