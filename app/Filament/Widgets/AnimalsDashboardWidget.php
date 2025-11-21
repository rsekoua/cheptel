<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Animals\AnimalResource;
use App\Models\Animal;
use Filament\Widgets\Widget;

class AnimalsDashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.animals-dashboard-widget';

    protected int|string|array $columnSpan = 1;

    public function getAnimalsData(): array
    {
        return [
            'total' => Animal::where('is_actif', true)->count(),
            'truies' => Animal::where('is_actif', true)->where('type_animal', 'truie')->count(),
            'verrats' => Animal::where('is_actif', true)->where('type_animal', 'verrat')->count(),
            'url' => AnimalResource::getUrl('index'),
        ];
    }
}
