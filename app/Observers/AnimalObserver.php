<?php

namespace App\Observers;

use App\Models\Animal;
use App\Models\CycleReproduction;

class AnimalObserver
{
    /**
     * Handle the Animal "updated" event.
     */
    public function updated(Animal $animal): void
    {
        // Vérifier si le statut a changé
        if (! $animal->isDirty('statut_actuel')) {
            return;
        }

        $nouveauStatut = $animal->statut_actuel;

        // Conditions pour créer un nouveau cycle de reproduction
        $doitCreerCycle = match ($nouveauStatut) {
            // Truie vient de sevrer sa portée
            'sevree' => in_array($animal->type_animal, ['truie', 'cochette']),
            // Cochette a ses premières chaleurs
            'en_chaleurs' => $animal->type_animal === 'cochette' && $this->estPremiereChaleur($animal),
            default => false,
        };

        if ($doitCreerCycle) {
            $this->creerNouveauCycle($animal);
        }
    }

    /**
     * Créer un nouveau cycle de reproduction automatiquement
     */
    protected function creerNouveauCycle(Animal $animal): void
    {
        // Calculer le numéro de cycle (nombre de cycles existants + 1)
        $dernierCycle = CycleReproduction::where('animal_id', $animal->id)
            ->orderBy('numero_cycle', 'desc')
            ->first();

        $numeroCycle = $dernierCycle ? $dernierCycle->numero_cycle + 1 : 1;

        // Créer le nouveau cycle
        CycleReproduction::create([
            'animal_id' => $animal->id,
            'numero_cycle' => $numeroCycle,
            'date_debut' => now(),
            'statut_cycle' => 'en_cours',
            'resultat_diagnostic' => 'en_attente',
        ]);
    }

    /**
     * Vérifier si c'est la première chaleur de la cochette
     */
    protected function estPremiereChaleur(Animal $animal): bool
    {
        // Si c'est une cochette sans cycles de reproduction, c'est sa première chaleur
        return $animal->type_animal === 'cochette' && ! CycleReproduction::where('animal_id', $animal->id)->exists();
    }
}
