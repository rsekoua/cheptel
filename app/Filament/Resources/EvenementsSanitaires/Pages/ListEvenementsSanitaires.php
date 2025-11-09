<?php

namespace App\Filament\Resources\EvenementsSanitaires\Pages;

use App\Filament\Resources\EvenementsSanitaires\EvenementsSanitaireResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvenementsSanitaires extends ListRecords
{
    protected static string $resource = EvenementsSanitaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
