<?php

namespace App\Filament\Resources\TypeSalles\Pages;

use App\Filament\Resources\TypeSalles\TypeSalleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTypeSalle extends ViewRecord
{
    protected static string $resource = TypeSalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
