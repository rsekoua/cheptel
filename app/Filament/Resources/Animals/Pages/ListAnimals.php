<?php

namespace App\Filament\Resources\Animals\Pages;

use App\Filament\Resources\Animals\AnimalResource;
use App\Filament\Resources\Animals\Schemas\AnimalForm;
use App\Filament\Resources\Animals\Widgets\AnimalStatsOverview;
use App\Models\Animal;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ListAnimals extends ListRecords
{
    protected static string $resource = AnimalResource::class;

    protected static ?string $title = 'Gestion des animaux';

    protected string $view = 'filament.resources.animals.pages.list-animals';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function createAction(): Action
    {
        return Action::make('create')
            ->label('Créer un animal')
            ->modalHeading('Nouvel animal')
            ->modalWidth('4xl')
            ->schema(fn (Schema $form) => AnimalForm::configure($form))
            ->action(function (array $data): void {
                Animal::create($data);

                Notification::make()
                    ->title('Animal créé avec succès')
                    ->success()
                    ->send();
            });
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
