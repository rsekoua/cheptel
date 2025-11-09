<?php

namespace App\Filament\Resources\Lots\RelationManagers;

use App\Filament\Resources\EvenementsAlimentations\EvenementsAlimentationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class EvenementsAlimentationRelationManager extends RelationManager
{
    protected static string $relationship = 'evenementsAlimentation';

    protected static ?string $relatedResource = EvenementsAlimentationResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
