<?php

namespace App\Filament\Resources\StockAliments\Widgets;

use App\Models\StockAliment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockValuationWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $entrepotStock = StockAliment::where('type_stock', 'entrepot')->get();
        $preparationStock = StockAliment::where('type_stock', 'preparation')->get();

        $entrepotValeur = $entrepotStock->sum('valeur_stock');
        $entrepotPoids = $entrepotStock->sum('poids_kg_disponible');

        $preparationValeur = $preparationStock->sum('valeur_stock');
        $preparationPoids = $preparationStock->sum('poids_kg_disponible');

        $totalValeur = $entrepotValeur + $preparationValeur;
        $totalPoids = $entrepotPoids + $preparationPoids;

        $nombreAliments = StockAliment::whereHas('aliment', function ($query) {
            $query->where('actif', true);
        })->distinct('aliment_id')->count('aliment_id');

        return [
            Stat::make('Valeur stock total', number_format($totalValeur, 0, ',', ' ').' FCFA')
                ->description(number_format($totalPoids, 2, ',', ' ').' kg disponibles')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Stock entrepôt', number_format($entrepotValeur, 0, ',', ' ').' FCFA')
                ->description(number_format($entrepotPoids, 2, ',', ' ').' kg')
                ->descriptionIcon('heroicon-o-building-storefront')
                ->color('primary'),

            Stat::make('Stock préparation', number_format($preparationValeur, 0, ',', ' ').' FCFA')
                ->description(number_format($preparationPoids, 2, ',', ' ').' kg')
                ->descriptionIcon('heroicon-o-beaker')
                ->color('warning'),

            Stat::make('Aliments actifs', $nombreAliments)
                ->description('Types d\'aliments en stock')
                ->descriptionIcon('heroicon-o-squares-2x2')
                ->color('info'),
        ];
    }
}
