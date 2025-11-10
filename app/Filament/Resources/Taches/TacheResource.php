<?php

namespace App\Filament\Resources\Taches;

use App\Filament\Resources\Taches\Pages\CreateTache;
use App\Filament\Resources\Taches\Pages\EditTache;
use App\Filament\Resources\Taches\Pages\ListTaches;
use App\Filament\Resources\Taches\Pages\ViewTache;
use App\Filament\Resources\Taches\Schemas\TacheForm;
use App\Filament\Resources\Taches\Schemas\TacheInfolist;
use App\Filament\Resources\Taches\Tables\TachesTable;
use App\Models\Tache;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TacheResource extends Resource
{
    protected static ?string $model = Tache::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TacheForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TacheInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TachesTable::configure($table);
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
            'index' => ListTaches::route('/'),
            'create' => CreateTache::route('/create'),
            'view' => ViewTache::route('/{record}'),
            'edit' => EditTache::route('/{record}/edit'),
        ];
    }
}
