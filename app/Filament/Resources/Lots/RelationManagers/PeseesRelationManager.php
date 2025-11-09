<?php

namespace App\Filament\Resources\Lots\RelationManagers;

use App\Filament\Resources\Pesees\PeseeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PeseesRelationManager extends RelationManager
{
    protected static string $relationship = 'pesees';

    protected static ?string $relatedResource = PeseeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
