<?php

namespace App\Filament\Resources\Pesees;

use App\Filament\Resources\Pesees\Pages\CreatePesee;
use App\Filament\Resources\Pesees\Pages\EditPesee;
use App\Filament\Resources\Pesees\Pages\ListPesees;
use App\Filament\Resources\Pesees\Pages\ViewPesee;
use App\Filament\Resources\Pesees\Schemas\PeseeForm;
use App\Filament\Resources\Pesees\Schemas\PeseeInfolist;
use App\Filament\Resources\Pesees\Tables\PeseesTable;
use App\Models\Pesee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PeseeResource extends Resource
{
    protected static ?string $model = Pesee::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PeseeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PeseeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeseesTable::configure($table);
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
            'index' => ListPesees::route('/'),
            'create' => CreatePesee::route('/create'),
            'view' => ViewPesee::route('/{record}'),
            'edit' => EditPesee::route('/{record}/edit'),
        ];
    }
}
