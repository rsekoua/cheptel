<?php

namespace App\Filament\Resources\Lots;

use App\Filament\Resources\Lots\Pages\CreateLot;
use App\Filament\Resources\Lots\Pages\EditLot;
use App\Filament\Resources\Lots\Pages\ListLots;
use App\Filament\Resources\Lots\Pages\ViewLot;
use App\Filament\Resources\Lots\RelationManagers\EvenementsAlimentationRelationManager;
use App\Filament\Resources\Lots\RelationManagers\EvenementsSanitairesRelationManager;
use App\Filament\Resources\Lots\RelationManagers\MouvementsRelationManager;
use App\Filament\Resources\Lots\RelationManagers\PeseesRelationManager;
use App\Filament\Resources\Lots\RelationManagers\PorteesRelationManager;
use App\Filament\Resources\Lots\RelationManagers\TachesRelationManager;
use App\Filament\Resources\Lots\Schemas\LotForm;
use App\Filament\Resources\Lots\Schemas\LotInfolist;
use App\Filament\Resources\Lots\Tables\LotsTable;
use App\Models\Lot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LotResource extends Resource
{
    protected static ?string $model = Lot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static string|null|\UnitEnum $navigationGroup = 'Production';

    protected static ?string $modelLabel = 'Lot';

    protected static ?string $pluralModelLabel = 'Lots';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return LotForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LotInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LotsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PorteesRelationManager::class,
            PeseesRelationManager::class,
            EvenementsSanitairesRelationManager::class,
            EvenementsAlimentationRelationManager::class,
            MouvementsRelationManager::class,
            TachesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLots::route('/'),
            'create' => CreateLot::route('/create'),
            'view' => ViewLot::route('/{record}'),
            'edit' => EditLot::route('/{record}/edit'),
        ];
    }
}
