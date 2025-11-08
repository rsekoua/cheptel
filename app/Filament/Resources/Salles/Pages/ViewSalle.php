<?php

namespace App\Filament\Resources\Salles\Pages;

use App\Filament\Resources\Salles\SalleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSalle extends ViewRecord
{
    protected static string $resource = SalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
