<?php

namespace App\Filament\Resources\EvenementsAlimentations\Pages;

use App\Filament\Resources\EvenementsAlimentations\EvenementsAlimentationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvenementsAlimentation extends ViewRecord
{
    protected static string $resource = EvenementsAlimentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
