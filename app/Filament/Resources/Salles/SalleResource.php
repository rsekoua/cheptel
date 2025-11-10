<?php

namespace App\Filament\Resources\Salles;

use App\Filament\Resources\Salles\Pages\CreateSalle;
use App\Filament\Resources\Salles\Pages\EditSalle;
use App\Filament\Resources\Salles\Pages\ListSalles;
use App\Filament\Resources\Salles\Pages\ViewSalle;
use App\Filament\Resources\Salles\Schemas\SalleForm;
use App\Filament\Resources\Salles\Schemas\SalleInfolist;
use App\Filament\Resources\Salles\Tables\SallesTable;
use App\Models\Salle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalleResource extends Resource
{
    protected static ?string $model = Salle::class;

    //    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|null|\UnitEnum $navigationGroup = 'Parametres';

    public static function form(Schema $schema): Schema
    {
        return SalleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SallesTable::configure($table);
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
            'index' => ListSalles::route('/'),
            //            'create' => CreateSalle::route('/create'),
            //            'view' => ViewSalle::route('/{record}'),
            //            'edit' => EditSalle::route('/{record}/edit'),
        ];
    }
}
