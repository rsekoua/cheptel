<?php

namespace App\Filament\Resources\Animals\RelationManagers;

use App\Filament\Resources\CycleReproductions\CycleReproductionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class CyclesReproductionRelationManager extends RelationManager
{
    protected static string $relationship = 'cyclesReproduction';

    protected static ?string $relatedResource = CycleReproductionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
