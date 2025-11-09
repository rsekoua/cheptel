<?php

namespace App\Filament\Resources\Portees\Pages;

use App\Filament\Resources\Portees\PorteeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPortees extends ListRecords
{
    protected static string $resource = PorteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
