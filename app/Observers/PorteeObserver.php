<?php

namespace App\Observers;

use App\Models\Portee;

class PorteeObserver
{
    /**
     * Handle the Portee "created" event.
     */
    public function created(Portee $portee): void
    {
        //
    }

    /**
     * Handle the Portee "updated" event.
     */
    public function updated(Portee $portee): void
    {
        //
    }

    /**
     * Handle the Portee "deleted" event.
     */
    public function deleted(Portee $portee): void
    {
        // Restaurer le statut du cycle de reproduction à "gestation_en_cours"
        if ($portee->cycleReproduction) {
            $portee->cycleReproduction->update([
                'statut_cycle' => 'gestation_en_cours',
                'date_mise_bas_reelle' => null,
            ]);
        }
    }

    /**
     * Handle the Portee "restored" event.
     */
    public function restored(Portee $portee): void
    {
        //
    }

    /**
     * Handle the Portee "force deleted" event.
     */
    public function forceDeleted(Portee $portee): void
    {
        //
    }
}
