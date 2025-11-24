<?php

namespace App\Filament\Resources\StockAliments;

use App\Filament\Resources\StockAliments\Pages\ListStockAliments;
use App\Filament\Resources\StockAliments\Pages\ViewStockAliment;
use App\Filament\Resources\StockAliments\Schemas\StockAlimentInfolist;
use App\Filament\Resources\StockAliments\Tables\StockAlimentsTable;
use App\Models\StockAliment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockAlimentResource extends Resource
{
    protected static ?string $model = StockAliment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCubeTransparent;

    protected static string|null|\UnitEnum $navigationGroup = 'Gestion Aliments';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return 'Stock';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Stocks';
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockAlimentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAlimentsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
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
            'index' => ListStockAliments::route('/'),
            'view' => ViewStockAliment::route('/{record}'),
        ];
    }
}
