<?php

namespace App\Filament\Resources\CycleReproductions\Pages;

use App\Filament\Resources\CycleReproductions\CycleReproductionResource;
use App\Models\CycleReproduction;
use App\Models\Portee;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListCycleReproductions extends ListRecords
{
    protected static string $resource = CycleReproductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus)
                ->color('primary')
                ->label('')
                ->size('sm'),
        ];
    }

    public function getTabs(): array
    {

        $nombrePorcelets = Portee::all()->sum(fn (Portee $portee) => $portee->nb_total);
        return [
            'Tous' => Tab::make(),
            'En gestation' => Tab::make()
                //->icon('heroicon-m-user-group')
                ->iconPosition(IconPosition::After)
                ->badge(CycleReproduction::query()->where('statut_cycle', '=', 'gestation_en_cours')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('statut_cycle', '=', 'gestation_en_cours')),
            'En lactation' => Tab::make()
                //->icon('heroicon-m-user-group')
                ->iconPosition(IconPosition::After)
                ->badge(CycleReproduction::query()->where('statut_cycle', '=', 'en_lactation')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('statut_cycle', '=', 'en_lactation')),
            'Portées' => Tab::make()
                //->icon('heroicon-m-user-group')
                ->iconPosition(IconPosition::After)
                ->badge($nombrePorcelets)
                //->modifyQueryUsing(fn (Builder $query) => $query->where('statut_cycle', '=', 'en_lactation')),

//            'Verrat' => Tab::make()
////
//                ->badge(Animal::query()->where('type_animal', '=', 'verrat')->count())
//                ->modifyQueryUsing(fn (Builder $query) => $query->where('type_animal', '=', 'verrat')),
        ];
    }
}


