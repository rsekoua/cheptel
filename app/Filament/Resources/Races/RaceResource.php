<?php

namespace App\Filament\Resources\Races;

use App\Filament\Resources\Races\Pages\CreateRace;
use App\Filament\Resources\Races\Pages\EditRace;
use App\Filament\Resources\Races\Pages\ListRaces;
use App\Filament\Resources\Races\Pages\ViewRace;
use App\Filament\Resources\Races\Schemas\RaceForm;
use App\Filament\Resources\Races\Schemas\RaceInfolist;
use App\Filament\Resources\Races\Tables\RacesTable;
use App\Models\Race;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RaceResource extends Resource
{
    protected static ?string $model = Race::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = null;

    //    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|null|\UnitEnum $navigationGroup = 'Parametres';

    public static function form(Schema $schema): Schema
    {
        return RaceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RaceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RacesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRaces::route('/'),
            //            'create' => CreateRace::route('/create'),
            //            'view' => ViewRace::route('/{record}'),
            //            'edit' => EditRace::route('/{record}/edit'),
        ];
    }
}
