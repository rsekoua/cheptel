<?php

namespace App\Filament\Resources\CycleReproductions\Pages;

use App\Filament\Resources\CycleReproductions\CycleReproductionResource;
use App\Models\CycleReproduction;
use Filament\Resources\Pages\CreateRecord;

class CreateCycleReproduction extends CreateRecord
{
    protected static string $resource = CycleReproductionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-incrémenter le numéro de cycle pour cet animal
        if (isset($data['animal_id'])) {
            $dernierCycle = CycleReproduction::where('animal_id', $data['animal_id'])
                ->max('numero_cycle');

            $data['numero_cycle'] = ($dernierCycle ?? 0) + 1;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Mettre à jour date_premiere_saillie après la création si des saillies ont été ajoutées
        $premiereSaillie = $this->record->saillies()->orderBy('date_heure')->first();

        if ($premiereSaillie) {
            $this->record->update([
                'date_premiere_saillie' => $premiereSaillie->date_heure,
            ]);
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pré-remplir l'animal_id si passé en paramètre d'URL
        if (request()->has('animal_id')) {
            $data['animal_id'] = request()->get('animal_id');
        }

        return $data;
    }
}
