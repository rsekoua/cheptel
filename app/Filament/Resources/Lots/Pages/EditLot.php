<?php

namespace App\Filament\Resources\Lots\Pages;

use App\Filament\Resources\Lots\LotResource;
use App\Models\Portee;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLot extends EditRecord
{
    protected static string $resource = LotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->syncPorteesWithPivotData();
    }

    protected function syncPorteesWithPivotData(): void
    {
        $porteeIds = $this->data['portees'] ?? [];

        if (empty($porteeIds)) {
            $this->record->portees()->sync([]);

            return;
        }

        $syncData = [];
        foreach ($porteeIds as $porteeId) {
            $portee = Portee::find($porteeId);
            if ($portee) {
                $syncData[$porteeId] = [
                    'nb_porcelets_transferes' => $portee->nb_sevres ?? 0,
                    'poids_total_transfere_kg' => $portee->poids_total_sevrage_kg ?? 0,
                ];
            }
        }

        $this->record->portees()->sync($syncData);
    }
}
