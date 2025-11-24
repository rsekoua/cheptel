<?php

namespace App\Filament\Resources\PreparationAliments\Pages;

use App\Filament\Resources\PreparationAliments\PreparationAlimentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPreparationAliment extends EditRecord
{
    protected static string $resource = PreparationAlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
