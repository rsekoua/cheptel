<?php

namespace App\Filament\Resources\ProduitSanitaires\Pages;

use App\Filament\Resources\ProduitSanitaires\ProduitSanitaireResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProduitSanitaire extends EditRecord
{
    protected static string $resource = ProduitSanitaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
