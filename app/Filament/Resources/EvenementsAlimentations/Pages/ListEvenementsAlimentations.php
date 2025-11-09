<?php

namespace App\Filament\Resources\EvenementsAlimentations\Pages;

use App\Filament\Resources\EvenementsAlimentations\EvenementsAlimentationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvenementsAlimentations extends ListRecords
{
    protected static string $resource = EvenementsAlimentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
