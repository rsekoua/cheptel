<?php

namespace App\Filament\Resources\Taches\Pages;

use App\Filament\Resources\Taches\TacheResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTache extends ViewRecord
{
    protected static string $resource = TacheResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
