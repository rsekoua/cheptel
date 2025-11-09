<?php

namespace App\Filament\Resources\Animals\RelationManagers;

use App\Filament\Resources\EvenementsSanitaires\EvenementsSanitaireResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class EvenementsSanitairesRelationManager extends RelationManager
{
    protected static string $relationship = 'evenementsSanitaires';

    protected static ?string $relatedResource = EvenementsSanitaireResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
