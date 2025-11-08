<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mouvement extends Model
{
    protected $fillable = [
        'type_cible',
        'animal_id',
        'lot_id',
        'date_mouvement',
        'salle_origine_id',
        'salle_destination_id',
        'place_numero',
        'motif',
        'nb_animaux',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_mouvement' => 'datetime',
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

    public function salleOrigine(): BelongsTo
    {
        return $this->belongsTo(Salle::class, 'salle_origine_id');
    }

    public function salleDestination(): BelongsTo
    {
        return $this->belongsTo(Salle::class, 'salle_destination_id');
    }
}
