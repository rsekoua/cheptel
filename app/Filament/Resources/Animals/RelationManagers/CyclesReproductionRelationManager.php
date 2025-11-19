<?php

namespace App\Filament\Resources\Animals\RelationManagers;

use App\Filament\Resources\CycleReproductions\CycleReproductionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class CyclesReproductionRelationManager extends RelationManager
{
    protected static string $relationship = 'cyclesReproduction';

    protected static ?string $relatedResource = CycleReproductionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->visible(function (): bool {
                        // Masquer le bouton si un cycle actif existe déjà
                        return ! $this->ownerRecord->cyclesReproduction()
                            ->whereNotIn('statut_cycle', ['termine_succes', 'termine_echec'])
                            ->exists();
                    }),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pré-remplir l'animal_id avec l'animal propriétaire du RelationManager
        $data['animal_id'] = $this->ownerRecord->id;

        return $data;
    }
}
