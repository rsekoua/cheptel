<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvenementSanitaire extends Model
{
    protected $fillable = [
        'type_cible',
        'animal_id',
        'lot_id',
        'date_evenement',
        'type_evenement',
        'produit_sanitaire_id',
        'dose_administree',
        'nb_animaux_traites',
        'intervenant',
        'motif',
        'cout_total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_evenement' => 'datetime',
            'dose_administree' => 'decimal:3',
            'cout_total' => 'decimal:2',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function produitSanitaire(): BelongsTo
    {
        return $this->belongsTo(ProduitSanitaire::class);
    }
}
