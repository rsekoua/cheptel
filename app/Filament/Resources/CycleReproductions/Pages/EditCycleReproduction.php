<?php

namespace App\Filament\Resources\CycleReproductions\Pages;

use App\Filament\Resources\CycleReproductions\CycleReproductionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCycleReproduction extends EditRecord
{
    protected static string $resource = CycleReproductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
