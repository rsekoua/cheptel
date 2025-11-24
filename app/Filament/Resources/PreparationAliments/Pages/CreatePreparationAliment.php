<?php

namespace App\Filament\Resources\PreparationAliments\Pages;

use App\Filament\Resources\PreparationAliments\PreparationAlimentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePreparationAliment extends CreateRecord
{
    protected static string $resource = PreparationAlimentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
