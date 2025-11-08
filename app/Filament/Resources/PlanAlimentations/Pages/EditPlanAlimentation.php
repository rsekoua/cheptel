<?php

namespace App\Filament\Resources\PlanAlimentations\Pages;

use App\Filament\Resources\PlanAlimentations\PlanAlimentationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPlanAlimentation extends EditRecord
{
    protected static string $resource = PlanAlimentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
