<?php

namespace App\Filament\Resources\PreparationAliments;

use App\Filament\Resources\PreparationAliments\Pages\CreatePreparationAliment;
use App\Filament\Resources\PreparationAliments\Pages\EditPreparationAliment;
use App\Filament\Resources\PreparationAliments\Pages\ListPreparationAliments;
use App\Filament\Resources\PreparationAliments\Pages\ViewPreparationAliment;
use App\Filament\Resources\PreparationAliments\Schemas\PreparationAlimentForm;
use App\Filament\Resources\PreparationAliments\Schemas\PreparationAlimentInfolist;
use App\Filament\Resources\PreparationAliments\Tables\PreparationAlimentsTable;
use App\Models\PreparationAliment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PreparationAlimentResource extends Resource
{
    protected static ?string $model = PreparationAliment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|null|\UnitEnum $navigationGroup = 'Gestion Aliments';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'Préparation';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Préparations';
    }

    public static function form(Schema $schema): Schema
    {
        return PreparationAlimentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PreparationAlimentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PreparationAlimentsTable::configure($table);
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
            'index' => ListPreparationAliments::route('/'),
            'create' => CreatePreparationAliment::route('/create'),
            'view' => ViewPreparationAliment::route('/{record}'),
            'edit' => EditPreparationAliment::route('/{record}/edit'),
        ];
    }
}
