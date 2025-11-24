<?php

namespace App\Observers;

use App\Models\DetailPreparation;
use App\Models\StockAliment;

class DetailPreparationObserver
{
    /**
     * Handle the DetailPreparation "creating" event.
     */
    public function creating(DetailPreparation $detailPreparation): void
    {
        $this->ensureCoutUnitaire($detailPreparation);
        $this->calculateValeur($detailPreparation);
    }

    /**
     * Handle the DetailPreparation "updating" event.
     */
    public function updating(DetailPreparation $detailPreparation): void
    {
        $this->ensureCoutUnitaire($detailPreparation);
        $this->calculateValeur($detailPreparation);
    }

    /**
     * Handle the DetailPreparation "created" event.
     */
    public function created(DetailPreparation $detailPreparation): void
    {
        $this->updatePreparationTotals($detailPreparation);
    }

    /**
     * Handle the DetailPreparation "updated" event.
     */
    public function updated(DetailPreparation $detailPreparation): void
    {
        $this->updatePreparationTotals($detailPreparation);
    }

    /**
     * Handle the DetailPreparation "deleted" event.
     */
    public function deleted(DetailPreparation $detailPreparation): void
    {
        $this->updatePreparationTotals($detailPreparation);
    }

    /**
     * Ensure cout_unitaire_kg is set from stock if not provided.
     */
    protected function ensureCoutUnitaire(DetailPreparation $detailPreparation): void
    {
        if (! $detailPreparation->cout_unitaire_kg && $detailPreparation->aliment_id) {
            $stockPreparation = StockAliment::where('aliment_id', $detailPreparation->aliment_id)
                ->where('type_stock', 'preparation')
                ->first();

            if ($stockPreparation && $stockPreparation->cout_moyen_kg > 0) {
                $detailPreparation->cout_unitaire_kg = $stockPreparation->cout_moyen_kg;
            } else {
                $detailPreparation->cout_unitaire_kg = 0;
            }
        }
    }

    /**
     * Calculate the value of the detail.
     */
    protected function calculateValeur(DetailPreparation $detailPreparation): void
    {
        if ($detailPreparation->quantite_kg && $detailPreparation->cout_unitaire_kg) {
            $detailPreparation->valeur = $detailPreparation->quantite_kg * $detailPreparation->cout_unitaire_kg;
        }
    }

    /**
     * Update the preparation totals.
     */
    protected function updatePreparationTotals(DetailPreparation $detailPreparation): void
    {
        if ($detailPreparation->preparation) {
            $preparation = $detailPreparation->preparation;

            $totals = $preparation->details()
                ->selectRaw('SUM(quantite_kg) as total_quantite, SUM(valeur) as total_cout')
                ->first();

            $preparation->update([
                'quantite_totale' => $totals->total_quantite ?? 0,
                'cout_total' => $totals->total_cout ?? 0,
            ]);
        }
    }
}
