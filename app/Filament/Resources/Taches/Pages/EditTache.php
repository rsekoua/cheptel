<?php

namespace App\Filament\Resources\Taches\Pages;

use App\Filament\Resources\Taches\TacheResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTache extends EditRecord
{
    protected static string $resource = TacheResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
