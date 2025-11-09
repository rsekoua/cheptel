<?php

namespace App\Filament\Resources\EvenementsSanitaires\Pages;

use App\Filament\Resources\EvenementsSanitaires\EvenementsSanitaireResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvenementsSanitaire extends ViewRecord
{
    protected static string $resource = EvenementsSanitaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
