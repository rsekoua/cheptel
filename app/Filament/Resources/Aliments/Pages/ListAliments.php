<?php

namespace App\Filament\Resources\Aliments\Pages;

use App\Filament\Resources\Aliments\AlimentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAliments extends ListRecords
{
    protected static string $resource = AlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
