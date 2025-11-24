<?php

namespace App\Observers;

use App\Models\PreparationAliment;
use Illuminate\Support\Facades\Auth;

class PreparationAlimentObserver
{
    /**
     * Handle the PreparationAliment "creating" event.
     */
    public function creating(PreparationAliment $preparationAliment): void
    {
        if (! $preparationAliment->user_id && Auth::check()) {
            $preparationAliment->user_id = Auth::id();
        }
    }
}
