<?php

namespace App\Filament\Resources\ProduitSanitaires\Pages;

use App\Filament\Resources\ProduitSanitaires\ProduitSanitaireResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduitSanitaire extends ViewRecord
{
    protected static string $resource = ProduitSanitaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
