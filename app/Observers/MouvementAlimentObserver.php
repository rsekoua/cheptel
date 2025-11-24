<?php

namespace App\Observers;

use App\Models\MouvementAliment;
use App\Models\StockAliment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MouvementAlimentObserver
{
    /**
     * Handle the MouvementAliment "creating" event.
     */
    public function creating(MouvementAliment $mouvementAliment): void
    {
        if (! $mouvementAliment->user_id && Auth::check()) {
            $mouvementAliment->user_id = Auth::id();
        }

        if ($mouvementAliment->type_mouvement === 'achat' && $mouvementAliment->cout_total && $mouvementAliment->poids_kg) {
            $mouvementAliment->cout_unitaire_kg = $mouvementAliment->cout_total / $mouvementAliment->poids_kg;
        }

        if (! $mouvementAliment->cout_unitaire_kg) {
            $stockSource = null;

            if (in_array($mouvementAliment->type_mouvement, ['transfert_entree', 'sortie_preparation'])) {
                $stockSource = StockAliment::where('aliment_id', $mouvementAliment->aliment_id)
                    ->where('type_stock', $mouvementAliment->type_stock === 'preparation' ? 'entrepot' : $mouvementAliment->type_stock)
                    ->first();
            } elseif ($mouvementAliment->type_mouvement === 'transfert_sortie') {
                $stockSource = StockAliment::where('aliment_id', $mouvementAliment->aliment_id)
                    ->where('type_stock', 'preparation')
                    ->first();
            }

            if ($stockSource && $stockSource->cout_moyen_kg > 0) {
                $mouvementAliment->cout_unitaire_kg = $stockSource->cout_moyen_kg;
            } else {
                $mouvementAliment->cout_unitaire_kg = 0;
            }
        }

        if ($mouvementAliment->poids_kg) {
            $mouvementAliment->valeur_mouvement = $mouvementAliment->cout_unitaire_kg * $mouvementAliment->poids_kg;
        }
    }

    /**
     * Handle the MouvementAliment "created" event.
     */
    public function created(MouvementAliment $mouvementAliment): void
    {
        $this->updateStock($mouvementAliment, 'add');
    }

    /**
     * Handle the MouvementAliment "deleted" event.
     */
    public function deleted(MouvementAliment $mouvementAliment): void
    {
        $this->updateStock($mouvementAliment, 'remove');
    }

    /**
     * Update stock based on movement.
     */
    protected function updateStock(MouvementAliment $mouvement, string $operation): void
    {
        DB::transaction(function () use ($mouvement, $operation) {
            $stock = StockAliment::firstOrCreate(
                [
                    'aliment_id' => $mouvement->aliment_id,
                    'type_stock' => $mouvement->type_stock,
                ],
                [
                    'nombre_sacs_disponibles' => 0,
                    'poids_kg_disponible' => 0,
                    'cout_moyen_kg' => 0,
                    'valeur_stock' => 0,
                ]
            );

            $multiplier = $operation === 'add' ? 1 : -1;

            $typesAjoutStock = ['achat', 'transfert_entree', 'transfert_sortie'];
            $typesRetraitStock = ['sortie_preparation'];

            if ($mouvement->type_stock === 'entrepot') {
                $typesAjoutStock = ['achat', 'transfert_sortie'];
                $typesRetraitStock = ['transfert_entree', 'sortie_preparation'];
            } elseif ($mouvement->type_stock === 'preparation') {
                $typesAjoutStock = ['transfert_entree'];
                $typesRetraitStock = ['transfert_sortie', 'sortie_preparation'];
            }

            if (in_array($mouvement->type_mouvement, $typesAjoutStock)) {
                $this->addToStock($stock, $mouvement, $multiplier);
            } elseif (in_array($mouvement->type_mouvement, $typesRetraitStock)) {
                $this->removeFromStock($stock, $mouvement, $multiplier);
            }

            if ($mouvement->type_mouvement === 'transfert_entree') {
                $stockEntrepot = StockAliment::where('aliment_id', $mouvement->aliment_id)
                    ->where('type_stock', 'entrepot')
                    ->first();

                if ($stockEntrepot) {
                    $this->removeFromStock($stockEntrepot, $mouvement, $multiplier);
                }
            } elseif ($mouvement->type_mouvement === 'transfert_sortie') {
                $stockPreparation = StockAliment::where('aliment_id', $mouvement->aliment_id)
                    ->where('type_stock', 'preparation')
                    ->first();

                if ($stockPreparation) {
                    $this->removeFromStock($stockPreparation, $mouvement, $multiplier);
                }
            }
        });
    }

    /**
     * Add quantity to stock with CUMP calculation.
     */
    protected function addToStock(StockAliment $stock, MouvementAliment $mouvement, int $multiplier): void
    {
        $ancienPoids = $stock->poids_kg_disponible;
        $ancienCout = $stock->cout_moyen_kg;
        $ancienneValeur = $stock->valeur_stock;

        $nouveauPoids = $ancienPoids + ($mouvement->poids_kg * $multiplier);
        $nouvelleValeur = $ancienneValeur + ($mouvement->valeur_mouvement * $multiplier);

        $nouveauCoutMoyen = $nouveauPoids > 0 ? $nouvelleValeur / $nouveauPoids : 0;

        $stock->update([
            'nombre_sacs_disponibles' => $stock->nombre_sacs_disponibles + ($mouvement->nombre_sacs * $multiplier),
            'poids_kg_disponible' => max(0, $nouveauPoids),
            'cout_moyen_kg' => $nouveauCoutMoyen,
            'valeur_stock' => max(0, $nouvelleValeur),
            'derniere_maj' => now(),
        ]);
    }

    /**
     * Remove quantity from stock.
     */
    protected function removeFromStock(StockAliment $stock, MouvementAliment $mouvement, int $multiplier): void
    {
        $nouveauPoids = $stock->poids_kg_disponible - ($mouvement->poids_kg * $multiplier);
        $nouvelleValeur = $nouveauPoids > 0 ? $nouveauPoids * $stock->cout_moyen_kg : 0;

        $stock->update([
            'nombre_sacs_disponibles' => max(0, $stock->nombre_sacs_disponibles - ($mouvement->nombre_sacs * $multiplier)),
            'poids_kg_disponible' => max(0, $nouveauPoids),
            'valeur_stock' => max(0, $nouvelleValeur),
            'derniere_maj' => now(),
        ]);
    }
}
