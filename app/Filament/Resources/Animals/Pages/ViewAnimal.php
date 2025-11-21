<?php

namespace App\Filament\Resources\Animals\Pages;

use App\Filament\Resources\Animals\AnimalResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAnimal extends ViewRecord
{
    protected static string $resource = AnimalResource::class;

    public function getTitle(): string
    {
        return 'Détails : '.$this->record->numero_identification;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
