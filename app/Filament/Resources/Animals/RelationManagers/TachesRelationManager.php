<?php

namespace App\Filament\Resources\Animals\RelationManagers;

use App\Filament\Resources\Taches\TacheResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class TachesRelationManager extends RelationManager
{
    protected static string $relationship = 'taches';

    protected static ?string $relatedResource = TacheResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
