<?php

namespace App\Filament\Resources\MouvementAliments\Pages;

use App\Filament\Resources\MouvementAliments\MouvementAlimentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMouvementAliment extends CreateRecord
{
    protected static string $resource = MouvementAlimentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
