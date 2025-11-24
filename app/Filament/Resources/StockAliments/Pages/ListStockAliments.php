<?php

namespace App\Filament\Resources\StockAliments\Pages;

use App\Filament\Resources\StockAliments\StockAlimentResource;
use App\Filament\Resources\StockAliments\Widgets\StockValuationWidget;
use Filament\Resources\Pages\ListRecords;

class ListStockAliments extends ListRecords
{
    protected static string $resource = StockAlimentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StockValuationWidget::class,
        ];
    }
}
