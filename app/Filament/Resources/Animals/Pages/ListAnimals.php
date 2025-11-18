<?php

namespace App\Filament\Resources\Animals\Pages;

use App\Filament\Resources\Animals\AnimalResource;
use App\Filament\Resources\Animals\Widgets\AnimalStatsOverview;
use App\Models\Animal;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ListAnimals extends ListRecords
{
    protected static string $resource = AnimalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
           // AnimalStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'Tous' => Tab::make(),
            'Truies' => Tab::make()->icon('heroicon-m-user-group')
                ->iconPosition(IconPosition::After)
                ->badge(Animal::query()->where('type_animal', '=', 'truie')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type_animal', '=', 'truie')),
            'Verrat' => Tab::make()

                ->badge(Animal::query()->where('type_animal', '=', 'verrat')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type_animal', '=', 'verrat')),
        ];
    }
}
