<?php

namespace App\Filament\Resources\EvenementsSanitaires\Pages;

use App\Filament\Resources\EvenementsSanitaires\EvenementsSanitaireResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEvenementsSanitaire extends EditRecord
{
    protected static string $resource = EvenementsSanitaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
