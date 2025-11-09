<?php

namespace App\Filament\Resources\Saillies;

use App\Filament\Resources\Saillies\Pages\CreateSaillie;
use App\Filament\Resources\Saillies\Pages\EditSaillie;
use App\Filament\Resources\Saillies\Pages\ListSaillies;
use App\Filament\Resources\Saillies\Pages\ViewSaillie;
use App\Filament\Resources\Saillies\Schemas\SaillieForm;
use App\Filament\Resources\Saillies\Schemas\SaillieInfolist;
use App\Filament\Resources\Saillies\Tables\SailliesTable;
use App\Models\Saillie;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SaillieResource extends Resource
{
    protected static ?string $model = Saillie::class;

    protected static string|BackedEnum|null $navigationIcon = null;
    protected static string|null|\UnitEnum $navigationGroup = 'Reproduction';

    public static function form(Schema $schema): Schema
    {
        return SaillieForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SaillieInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SailliesTable::configure($table);
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
            'index' => ListSaillies::route('/'),
//            'create' => CreateSaillie::route('/create'),
//            'view' => ViewSaillie::route('/{record}'),
//            'edit' => EditSaillie::route('/{record}/edit'),
        ];
    }
}
