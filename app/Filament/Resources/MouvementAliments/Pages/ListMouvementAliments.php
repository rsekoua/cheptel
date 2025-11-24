<?php

namespace App\Filament\Resources\MouvementAliments\Pages;

use App\Filament\Resources\MouvementAliments\MouvementAlimentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMouvementAliments extends ListRecords
{
    protected static string $resource = MouvementAlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
