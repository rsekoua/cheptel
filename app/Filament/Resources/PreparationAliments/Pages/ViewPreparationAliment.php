<?php

namespace App\Filament\Resources\PreparationAliments\Pages;

use App\Filament\Resources\PreparationAliments\PreparationAlimentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPreparationAliment extends ViewRecord
{
    protected static string $resource = PreparationAlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
