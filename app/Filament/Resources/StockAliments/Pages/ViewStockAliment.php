<?php

namespace App\Filament\Resources\StockAliments\Pages;

use App\Filament\Resources\StockAliments\StockAlimentResource;
use Filament\Resources\Pages\ViewRecord;

class ViewStockAliment extends ViewRecord
{
    protected static string $resource = StockAlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
