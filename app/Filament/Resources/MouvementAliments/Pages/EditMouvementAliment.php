<?php

namespace App\Filament\Resources\MouvementAliments\Pages;

use App\Filament\Resources\MouvementAliments\MouvementAlimentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMouvementAliment extends EditRecord
{
    protected static string $resource = MouvementAlimentResource::class;

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
