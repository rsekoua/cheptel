<?php

namespace App\Filament\Resources\PreparationAliments\Pages;

use App\Filament\Resources\PreparationAliments\PreparationAlimentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPreparationAliments extends ListRecords
{
    protected static string $resource = PreparationAlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
