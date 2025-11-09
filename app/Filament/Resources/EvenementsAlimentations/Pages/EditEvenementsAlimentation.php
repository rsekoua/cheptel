<?php

namespace App\Filament\Resources\EvenementsAlimentations\Pages;

use App\Filament\Resources\EvenementsAlimentations\EvenementsAlimentationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEvenementsAlimentation extends EditRecord
{
    protected static string $resource = EvenementsAlimentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
