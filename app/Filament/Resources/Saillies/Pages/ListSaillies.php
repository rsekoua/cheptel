<?php

namespace App\Filament\Resources\Saillies\Pages;

use App\Filament\Resources\Saillies\SaillieResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSaillies extends ListRecords
{
    protected static string $resource = SaillieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
