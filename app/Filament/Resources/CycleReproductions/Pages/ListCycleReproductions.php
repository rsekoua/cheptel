<?php

namespace App\Filament\Resources\CycleReproductions\Pages;

use App\Filament\Resources\CycleReproductions\CycleReproductionResource;
use App\Filament\Resources\CycleReproductions\Schemas\CycleReproductionForm;
use App\Models\CycleReproduction;
use App\Models\Portee;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ListCycleReproductions extends ListRecords
{
    protected static string $resource = CycleReproductionResource::class;

    protected static ?string $title = 'Gestion des cycles';

    protected string $view = 'filament.resources.cycle-reproductions.pages.list-cycle-reproductions';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function createAction(): Action
    {
        return Action::make('create')
            ->label('Créer un cycle')
            ->modalHeading('Nouveau cycle de reproduction')
            ->modalWidth('4xl')
            ->schema(fn (Schema $form) => CycleReproductionForm::configure($form))
            ->action(function (array $data): void {
                $cycle = CycleReproduction::create($data);

                if (isset($data['saillies'])) {
                    foreach ($data['saillies'] as $saillie) {
                        $cycle->saillies()->create($saillie);
                    }
                }

                Notification::make()
                    ->title('Cycle créé avec succès')
                    ->success()
                    ->send();
            });
    }

    public function getTabs(): array
    {
        $nombrePorcelets = Portee::all()->sum(fn (Portee $portee) => $portee->nb_total);

        return [
            'Tous' => Tab::make(),
            'En gestation' => Tab::make()
                ->iconPosition(IconPosition::After)
                ->badge(CycleReproduction::query()->where('statut_cycle', '=', 'gestation_en_cours')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('statut_cycle', '=', 'gestation_en_cours')),
            'En lactation' => Tab::make()
                ->iconPosition(IconPosition::After)
                ->badge(CycleReproduction::query()->where('statut_cycle', '=', 'en_lactation')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('statut_cycle', '=', 'en_lactation')),
            'Portées' => Tab::make()
                ->iconPosition(IconPosition::After)
                ->badge($nombrePorcelets),
        ];
    }
}
