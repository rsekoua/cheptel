<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesee extends Model
{
    protected $fillable = [
        'type_cible',
        'animal_id',
        'lot_id',
        'date_pesee',
        'poids_total_kg',
        'nb_animaux_peses',
        'methode',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_pesee' => 'date',
            'poids_total_kg' => 'decimal:2',
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
}
