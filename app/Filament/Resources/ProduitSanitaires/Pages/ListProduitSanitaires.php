<?php

namespace App\Filament\Resources\ProduitSanitaires\Pages;

use App\Filament\Resources\ProduitSanitaires\ProduitSanitaireResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProduitSanitaires extends ListRecords
{
    protected static string $resource = ProduitSanitaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
