<?php

namespace App\Filament\Resources\TypeSalles\Pages;

use App\Filament\Resources\TypeSalles\TypeSalleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTypeSalles extends ListRecords
{
    protected static string $resource = TypeSalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
