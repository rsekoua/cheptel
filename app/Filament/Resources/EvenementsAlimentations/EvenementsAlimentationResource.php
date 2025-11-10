<?php

namespace App\Filament\Resources\EvenementsAlimentations;

use App\Filament\Resources\EvenementsAlimentations\Pages\CreateEvenementsAlimentation;
use App\Filament\Resources\EvenementsAlimentations\Pages\EditEvenementsAlimentation;
use App\Filament\Resources\EvenementsAlimentations\Pages\ListEvenementsAlimentations;
use App\Filament\Resources\EvenementsAlimentations\Pages\ViewEvenementsAlimentation;
use App\Filament\Resources\EvenementsAlimentations\Schemas\EvenementsAlimentationForm;
use App\Filament\Resources\EvenementsAlimentations\Schemas\EvenementsAlimentationInfolist;
use App\Filament\Resources\EvenementsAlimentations\Tables\EvenementsAlimentationsTable;
use App\Models\EvenementsAlimentation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvenementsAlimentationResource extends Resource
{
    protected static ?string $model = EvenementsAlimentation::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EvenementsAlimentationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EvenementsAlimentationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvenementsAlimentationsTable::configure($table);
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
            'index' => ListEvenementsAlimentations::route('/'),
            'create' => CreateEvenementsAlimentation::route('/create'),
            'view' => ViewEvenementsAlimentation::route('/{record}'),
            'edit' => EditEvenementsAlimentation::route('/{record}/edit'),
        ];
    }
}
