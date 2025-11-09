<?php

namespace App\Filament\Resources\Saillies\Pages;

use App\Filament\Resources\Saillies\SaillieResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSaillie extends EditRecord
{
    protected static string $resource = SaillieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
