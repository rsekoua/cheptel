<?php

namespace App\Filament\Resources\PlanAlimentations\Pages;

use App\Filament\Resources\PlanAlimentations\PlanAlimentationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPlanAlimentation extends ViewRecord
{
    protected static string $resource = PlanAlimentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
