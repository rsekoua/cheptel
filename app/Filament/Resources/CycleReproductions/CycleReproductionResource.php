<?php

namespace App\Filament\Resources\CycleReproductions;

use App\Filament\Resources\CycleReproductions\Pages\CreateCycleReproduction;
use App\Filament\Resources\CycleReproductions\Pages\EditCycleReproduction;
use App\Filament\Resources\CycleReproductions\Pages\ListCycleReproductions;
use App\Filament\Resources\CycleReproductions\Pages\ViewCycleReproduction;
use App\Filament\Resources\CycleReproductions\Schemas\CycleReproductionForm;
use App\Filament\Resources\CycleReproductions\Schemas\CycleReproductionInfolist;
use App\Filament\Resources\CycleReproductions\Tables\CycleReproductionsTable;
use App\Models\CycleReproduction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CycleReproductionResource extends Resource
{
    protected static ?string $model = CycleReproduction::class;

    //    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|null|\UnitEnum $navigationGroup = 'Reproduction';

    protected static ?string $modelLabel = 'Cycle de reproduction';

    protected static ?string $pluralModelLabel = 'Cycles de reproduction';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CycleReproductionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CycleReproductionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CycleReproductionsTable::configure($table);
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
            'index' => ListCycleReproductions::route('/'),
            // La création manuelle est désactivée - les cycles sont créés automatiquement
            // create' => CreateCycleReproduction::route('/create'),
            'view' => ViewCycleReproduction::route('/{record}'),
            'edit' => EditCycleReproduction::route('/{record}/edit'),
        ];
    }

    /**
     * Désactiver la création manuelle de cycles
     * Les cycles sont créés automatiquement quand l'animal passe au statut approprié
     */
    public static function canCreate(): bool
    {
        return false;
    }
}
