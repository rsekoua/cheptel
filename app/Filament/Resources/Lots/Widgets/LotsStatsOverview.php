<?php

namespace App\Filament\Resources\Lots\Widgets;

use App\Models\Lot;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class LotsStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Lots actifs
        $lotsActifs = Lot::where('statut_lot', 'actif')->count();

        // Répartition post-sevrage / engraissement
        $lotsPostSevrage = Lot::where('statut_lot', 'actif')
            ->where('type_lot', 'post_sevrage')
            ->count();

        $lotsEngraissement = Lot::where('statut_lot', 'actif')
            ->where('type_lot', 'engraissement')
            ->count();

        // Effectif total
        $effectifTotal = Lot::where('statut_lot', 'actif')
            ->sum('nb_animaux_actuel');

        // GMQ moyen
        $lotsAvecGMQ = Lot::where('statut_lot', 'actif')
            ->whereNotNull('poids_moyen_depart_kg')
            ->whereNotNull('poids_moyen_actuel_kg')
            ->get()
            ->filter(fn ($lot) => $lot->gmq !== null);

        $gmqMoyen = $lotsAvecGMQ->isNotEmpty()
            ? round($lotsAvecGMQ->avg(fn ($lot) => $lot->gmq), 0)
            : null;

        // Taux de mortalité moyen
        $lotsAvecMortalite = Lot::where('statut_lot', 'actif')
            ->where('nb_animaux_depart', '>', 0)
            ->get()
            ->filter(fn ($lot) => $lot->taux_mortalite !== null);

        $tauxMortaliteMoyen = $lotsAvecMortalite->isNotEmpty()
            ? round($lotsAvecMortalite->avg(fn ($lot) => $lot->taux_mortalite), 2)
            : null;

        // Lots avec alerte mortalité élevée (> 5%)
        $lotsAlerteMortalite = $lotsAvecMortalite->filter(fn ($lot) => $lot->taux_mortalite > 5)->count();

        return [
            StatsOverviewWidget\Stat::make('Lots actifs', $lotsActifs)
                ->description("{$lotsPostSevrage} post-sevrage | {$lotsEngraissement} engraissement")
                ->descriptionIcon(Heroicon::OutlinedRectangleGroup)
                ->color('primary')
                ->chart([7, 12, 8, 15, 10, 18, $lotsActifs]),

            StatsOverviewWidget\Stat::make('Effectif total', number_format($effectifTotal))
                ->description('Animaux dans les lots actifs')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('success')
                ->chart([1200, 1450, 1380, 1550, 1420, 1650, $effectifTotal]),

            StatsOverviewWidget\Stat::make('GMQ moyen', $gmqMoyen !== null ? $gmqMoyen.' g/j' : 'N/A')
                ->description('Gain Moyen Quotidien')
                ->descriptionIcon(Heroicon::OutlinedChartBar)
                ->color($gmqMoyen !== null ? ($gmqMoyen >= 700 ? 'success' : ($gmqMoyen >= 600 ? 'warning' : 'danger')) : 'gray')
                ->chart([620, 650, 680, 690, 710, 720, $gmqMoyen ?? 0]),

            StatsOverviewWidget\Stat::make('Taux mortalité moyen', $tauxMortaliteMoyen !== null ? number_format($tauxMortaliteMoyen, 2).' %' : 'N/A')
                ->description($lotsAlerteMortalite > 0 ? "{$lotsAlerteMortalite} lot(s) en alerte (> 5%)" : 'Tous les lots OK')
                ->descriptionIcon($lotsAlerteMortalite > 0 ? Heroicon::OutlinedExclamationTriangle : Heroicon::OutlinedCheckCircle)
                ->color($tauxMortaliteMoyen !== null ? ($tauxMortaliteMoyen < 3 ? 'success' : ($tauxMortaliteMoyen < 5 ? 'warning' : 'danger')) : 'gray')
                ->chart([2.1, 2.3, 1.9, 2.8, 3.2, 2.5, $tauxMortaliteMoyen ?? 0]),
        ];
    }
}
