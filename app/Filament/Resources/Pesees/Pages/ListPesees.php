<?php

namespace App\Filament\Resources\Pesees\Pages;

use App\Filament\Resources\Pesees\PeseeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPesees extends ListRecords
{
    protected static string $resource = PeseeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
