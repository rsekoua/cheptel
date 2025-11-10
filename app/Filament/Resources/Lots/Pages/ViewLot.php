<?php

namespace App\Filament\Resources\Lots\Pages;

use App\Filament\Resources\Lots\Actions\CloturerLotAction;
use App\Filament\Resources\Lots\Actions\EnregistrerMortaliteAction;
use App\Filament\Resources\Lots\Actions\EnregistrerPeseeAction;
use App\Filament\Resources\Lots\Actions\TransfertEngraissementAction;
use App\Filament\Resources\Lots\LotResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLot extends ViewRecord
{
    protected static string $resource = LotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            EnregistrerPeseeAction::make(),
            EnregistrerMortaliteAction::make(),
            TransfertEngraissementAction::make(),
            CloturerLotAction::make(),
        ];
    }
}
