<?php

namespace App\Filament\Resources\Portees\Pages;

use App\Filament\Resources\Portees\PorteeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPortee extends ViewRecord
{
    protected static string $resource = PorteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
