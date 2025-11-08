<?php

namespace App\Filament\Resources\TypeSalles\Pages;

use App\Filament\Resources\TypeSalles\TypeSalleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTypeSalle extends EditRecord
{
    protected static string $resource = TypeSalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
