<?php

namespace App\Filament\Resources\CycleReproductions\Pages;

use App\Filament\Resources\CycleReproductions\Actions\EnregistrerMiseBasAction;
use App\Filament\Resources\CycleReproductions\CycleReproductionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCycleReproduction extends ViewRecord
{
    protected static string $resource = CycleReproductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EnregistrerMiseBasAction::make(),
            EditAction::make(),
        ];
    }
}
