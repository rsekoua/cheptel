<?php

namespace App\Filament\Resources\Portees\Pages;

use App\Filament\Resources\Portees\PorteeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPortee extends EditRecord
{
    protected static string $resource = PorteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
