<?php

namespace App\Filament\Resources\PlanAlimentations\Pages;

use App\Filament\Resources\PlanAlimentations\PlanAlimentationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlanAlimentations extends ListRecords
{
    protected static string $resource = PlanAlimentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
