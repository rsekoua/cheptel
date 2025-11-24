<?php

namespace App\Filament\Resources\MouvementAliments\Pages;

use App\Filament\Resources\MouvementAliments\MouvementAlimentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMouvementAliment extends ViewRecord
{
    protected static string $resource = MouvementAlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
