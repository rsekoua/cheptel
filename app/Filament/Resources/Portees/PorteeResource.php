<?php

namespace App\Filament\Resources\Portees;

use App\Filament\Resources\Portees\Pages\CreatePortee;
use App\Filament\Resources\Portees\Pages\EditPortee;
use App\Filament\Resources\Portees\Pages\ListPortees;
use App\Filament\Resources\Portees\Pages\ViewPortee;
use App\Filament\Resources\Portees\RelationManagers\TachesRelationManager;
use App\Filament\Resources\Portees\Schemas\PorteeForm;
use App\Filament\Resources\Portees\Schemas\PorteeInfolist;
use App\Filament\Resources\Portees\Tables\PorteesTable;
use App\Models\Portee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PorteeResource extends Resource
{
    protected static ?string $model = Portee::class;

//    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|null|\UnitEnum $navigationGroup = 'Reproduction';

    protected static ?string $modelLabel = 'Portée';

    protected static ?string $pluralModelLabel = 'Portées';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PorteeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PorteeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PorteesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TachesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPortees::route('/'),
            'create' => CreatePortee::route('/create'),
            'view' => ViewPortee::route('/{record}'),
            'edit' => EditPortee::route('/{record}/edit'),
        ];
    }
}
