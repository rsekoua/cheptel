<?php

namespace App\Filament\Resources\MouvementAliments;

use App\Filament\Resources\MouvementAliments\Pages\CreateMouvementAliment;
use App\Filament\Resources\MouvementAliments\Pages\EditMouvementAliment;
use App\Filament\Resources\MouvementAliments\Pages\ListMouvementAliments;
use App\Filament\Resources\MouvementAliments\Pages\ViewMouvementAliment;
use App\Filament\Resources\MouvementAliments\Schemas\MouvementAlimentForm;
use App\Filament\Resources\MouvementAliments\Schemas\MouvementAlimentInfolist;
use App\Filament\Resources\MouvementAliments\Tables\MouvementAlimentsTable;
use App\Models\MouvementAliment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MouvementAlimentResource extends Resource
{
    protected static ?string $model = MouvementAliment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|null|\UnitEnum $navigationGroup = 'Gestion Aliments';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Mouvement';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mouvements';
    }

    public static function form(Schema $schema): Schema
    {
        return MouvementAlimentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MouvementAlimentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MouvementAlimentsTable::configure($table);
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
            'index' => ListMouvementAliments::route('/'),
            'create' => CreateMouvementAliment::route('/create'),
            'view' => ViewMouvementAliment::route('/{record}'),
            'edit' => EditMouvementAliment::route('/{record}/edit'),
        ];
    }
}
