<?php

namespace App\Filament\Resources\Aliments\Pages;

use App\Filament\Resources\Aliments\AlimentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAliment extends CreateRecord
{
    protected static string $resource = AlimentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
