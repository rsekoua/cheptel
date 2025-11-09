<?php

namespace App\Filament\Resources\Saillies\Pages;

use App\Filament\Resources\Saillies\SaillieResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSaillie extends ViewRecord
{
    protected static string $resource = SaillieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
