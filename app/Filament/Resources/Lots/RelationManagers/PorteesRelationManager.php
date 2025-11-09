<?php

namespace App\Filament\Resources\Lots\RelationManagers;

use App\Filament\Resources\Portees\PorteeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PorteesRelationManager extends RelationManager
{
    protected static string $relationship = 'portees';

    protected static ?string $relatedResource = PorteeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
