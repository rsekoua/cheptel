<?php

namespace App\Filament\Resources\Animals\Widgets;

use App\Models\Animal;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnimalStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Truies en gestation', Animal::gestantes()->count())
                ->description('Truies en attente ou confirmées')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('warning'),

            Stat::make('Truies en lactation', Animal::enLactation()->count())
                ->description('Truies allaitantes')
                ->descriptionIcon('heroicon-o-heart')
                ->color('success'),
        ];
    }
}
