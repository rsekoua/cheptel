<?php

namespace App\Filament\Resources\Aliments;

use App\Filament\Resources\Aliments\Pages\CreateAliment;
use App\Filament\Resources\Aliments\Pages\EditAliment;
use App\Filament\Resources\Aliments\Pages\ListAliments;
use App\Filament\Resources\Aliments\Pages\ViewAliment;
use App\Filament\Resources\Aliments\Schemas\AlimentForm;
use App\Filament\Resources\Aliments\Schemas\AlimentInfolist;
use App\Filament\Resources\Aliments\Tables\AlimentsTable;
use App\Models\Aliment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AlimentResource extends Resource
{
    protected static ?string $model = Aliment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|null|\UnitEnum $navigationGroup = 'Gestion Aliments';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'Aliment';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Aliments';
    }

    public static function form(Schema $schema): Schema
    {
        return AlimentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AlimentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlimentsTable::configure($table);
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
            'index' => ListAliments::route('/'),
            'create' => CreateAliment::route('/create'),
            'view' => ViewAliment::route('/{record}'),
            'edit' => EditAliment::route('/{record}/edit'),
        ];
    }
}
