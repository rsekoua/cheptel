<?php

namespace App\Filament\Resources\Mouvements\Pages;

use App\Filament\Resources\Mouvements\MouvementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMouvement extends ViewRecord
{
    protected static string $resource = MouvementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
