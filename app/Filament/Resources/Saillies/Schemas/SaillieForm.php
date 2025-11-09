<?php

namespace App\Filament\Resources\Saillies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SaillieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cycle_reproduction_id')
                    ->relationship('cycleReproduction', 'id')
                    ->required(),
                DateTimePicker::make('date_heure')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                Select::make('verrat_id')
                    ->relationship('verrat', 'id'),
                TextInput::make('semence_lot_numero'),
                TextInput::make('intervenant'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
