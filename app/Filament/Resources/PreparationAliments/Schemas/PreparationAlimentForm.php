<?php

namespace App\Filament\Resources\PreparationAliments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
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
                            ->columns(3)
                            ->schema([
                                Select::make('aliment_id')
                                    ->label('Aliment')
                                    ->required()
                                    ->relationship('aliment', 'nom')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->columnSpan(1),

                                TextInput::make('quantite_kg')
                                    ->label('Quantité')
                                    ->required()
                                    ->numeric()
                                    ->suffix('kg')
                                    ->minValue(0.01)
                                    ->columnSpan(1),

                                TextInput::make('cout_unitaire_kg')
                                    ->label('Coût/kg')
                                    ->required()
                                    ->numeric()
                                    ->suffix('FCFA')
                                    ->minValue(0)
                                    ->columnSpan(1)
                                    ->helperText('Coût unitaire au moment de la sortie'),
                            ])
                            ->addActionLabel('Ajouter un aliment')
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
