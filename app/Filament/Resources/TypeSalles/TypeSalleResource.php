<?php

namespace App\Filament\Resources\TypeSalles;

use App\Filament\Resources\TypeSalles\Pages\CreateTypeSalle;
use App\Filament\Resources\TypeSalles\Pages\EditTypeSalle;
use App\Filament\Resources\TypeSalles\Pages\ListTypeSalles;
use App\Filament\Resources\TypeSalles\Pages\ViewTypeSalle;
use App\Filament\Resources\TypeSalles\Schemas\TypeSalleForm;
use App\Filament\Resources\TypeSalles\Schemas\TypeSalleInfolist;
use App\Filament\Resources\TypeSalles\Tables\TypeSallesTable;
use App\Models\TypeSalle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TypeSalleResource extends Resource
{
    protected static ?string $model = TypeSalle::class;

    //    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|null|\UnitEnum $navigationGroup = 'Parametres';

    public static function form(Schema $schema): Schema
    {
        return TypeSalleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TypeSalleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TypeSallesTable::configure($table);
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
            'index' => ListTypeSalles::route('/'),
            //            'create' => CreateTypeSalle::route('/create'),
            //            'view' => ViewTypeSalle::route('/{record}'),
            //            'edit' => EditTypeSalle::route('/{record}/edit'),
        ];
    }
}
