<?php

namespace App\Filament\Resources\Pesees\Pages;

use App\Filament\Resources\Pesees\PeseeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPesee extends EditRecord
{
    protected static string $resource = PeseeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
