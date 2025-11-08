<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvenementsAlimentation extends Model
{
    protected $table = 'evenements_alimentation';

    protected $fillable = [
        'lot_id',
        'animal_id',
        'date_debut',
        'date_fin',
        'plan_alimentation_id',
        'quantite_kg',
        'nb_animaux',
        'cout_total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'quantite_kg' => 'decimal:2',
            'cout_total' => 'decimal:2',
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function planAlimentation(): BelongsTo
    {
        return $this->belongsTo(PlanAlimentation::class);
    }
}
