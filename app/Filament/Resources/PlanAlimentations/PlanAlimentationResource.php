<?php

namespace App\Filament\Resources\PlanAlimentations;

use App\Filament\Resources\PlanAlimentations\Pages\CreatePlanAlimentation;
use App\Filament\Resources\PlanAlimentations\Pages\EditPlanAlimentation;
use App\Filament\Resources\PlanAlimentations\Pages\ListPlanAlimentations;
use App\Filament\Resources\PlanAlimentations\Pages\ViewPlanAlimentation;
use App\Filament\Resources\PlanAlimentations\Schemas\PlanAlimentationForm;
use App\Filament\Resources\PlanAlimentations\Schemas\PlanAlimentationInfolist;
use App\Filament\Resources\PlanAlimentations\Tables\PlanAlimentationsTable;
use App\Models\PlanAlimentation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlanAlimentationResource extends Resource
{
    protected static ?string $model = PlanAlimentation::class;

    //    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|null|\UnitEnum $navigationGroup = 'Parametres';

    public static function form(Schema $schema): Schema
    {
        return PlanAlimentationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PlanAlimentationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlanAlimentationsTable::configure($table);
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
            'index' => ListPlanAlimentations::route('/'),
            //            'create' => CreatePlanAlimentation::route('/create'),
            //            'view' => ViewPlanAlimentation::route('/{record}'),
            //            'edit' => EditPlanAlimentation::route('/{record}/edit'),
        ];
    }
}
