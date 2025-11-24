<?php

namespace App\Filament\Resources\Aliments\Pages;

use App\Filament\Resources\Aliments\AlimentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAliment extends ViewRecord
{
    protected static string $resource = AlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
