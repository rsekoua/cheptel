<?php

namespace App\Filament\Resources\CycleReproductions\Pages;

use App\Filament\Resources\CycleReproductions\CycleReproductionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCycleReproductions extends ListRecords
{
    protected static string $resource = CycleReproductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
