<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnimalsDashboardWidget;
use App\Filament\Widgets\AnimalsDashboardWidget2;
use App\Filament\Widgets\AnimalsDashboardWidget3;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Tableau de bord';

    protected static ?string $navigationLabel = 'Tableau de bord';

    protected static ?int $navigationSort = -2;


    public function getWidgets(): array
    {
        return [
            AnimalsDashboardWidget::class,
            AnimalsDashboardWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 2,
            'md' => 3,
            'lg' => 4,
        ];
    }
}
