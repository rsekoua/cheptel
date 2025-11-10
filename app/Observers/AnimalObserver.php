<?php

namespace App\Observers;

use App\Models\Animal;
use App\Models\CycleReproduction;
use Illuminate\Support\Facades\Log;

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

        $ancienStatut = $animal->getOriginal('statut_actuel');
        $nouveauStatut = $animal->statut_actuel;

        Log::info("AnimalObserver: Changement de statut pour {$animal->numero_identification}", [
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'type_animal' => $animal->type_animal,
        ]);

        // RÈGLE IMPORTANTE : Ne jamais créer de cycle si le dernier est encore en_cours
        if ($this->aCycleEnCours($animal)) {
            Log::info('AnimalObserver: Cycle en cours détecté, pas de création de nouveau cycle');

            return;
        }

        // Conditions pour créer un nouveau cycle de reproduction
        $doitCreerCycle = match ($nouveauStatut) {
            // Truie/Cochette vient de sevrer sa portée
            'sevree' => in_array($animal->type_animal, ['truie', 'cochette']),
            // Cochette a ses premières chaleurs OU truie revient en chaleurs après échec
            'en_chaleurs' => $this->doitCreerCyclePourChaleurs($animal),
            default => false,
        };

        if ($doitCreerCycle) {
            Log::info("AnimalObserver: Création d'un nouveau cycle pour {$animal->numero_identification}");
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
        $cycle = CycleReproduction::create([
            'animal_id' => $animal->id,
            'numero_cycle' => $numeroCycle,
            'date_debut' => now(),
            'statut_cycle' => 'en_cours',
            'resultat_diagnostic' => 'en_attente',
        ]);

        Log::info("AnimalObserver: Cycle #{$numeroCycle} créé pour {$animal->numero_identification}", [
            'cycle_id' => $cycle->id,
        ]);
    }

    /**
     * Vérifier si l'animal a déjà un cycle en cours
     */
    protected function aCycleEnCours(Animal $animal): bool
    {
        return CycleReproduction::where('animal_id', $animal->id)
            ->where('statut_cycle', 'en_cours')
            ->exists();
    }

    /**
     * Déterminer si on doit créer un cycle lors du passage en chaleurs
     */
    protected function doitCreerCyclePourChaleurs(Animal $animal): bool
    {
        // Cas 1 : Cochette sans aucun cycle (première chaleur)
        if ($animal->type_animal === 'cochette') {
            $nbCycles = CycleReproduction::where('animal_id', $animal->id)->count();
            if ($nbCycles === 0) {
                Log::info("AnimalObserver: Première chaleur détectée pour cochette {$animal->numero_identification}");

                return true;
            }
        }

        // Cas 2 : Truie/Cochette avec dernier cycle terminé en échec
        $dernierCycle = CycleReproduction::where('animal_id', $animal->id)
            ->orderBy('numero_cycle', 'desc')
            ->first();

        if ($dernierCycle && $dernierCycle->statut_cycle === 'termine_echec') {
            Log::info("AnimalObserver: Retour en chaleurs après échec pour {$animal->numero_identification}");

            return true;
        }

        return false;
    }
}
