<?php

namespace App\Filament\Resources\Pesees\Pages;

use App\Filament\Resources\Pesees\PeseeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPesee extends ViewRecord
{
    protected static string $resource = PeseeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
