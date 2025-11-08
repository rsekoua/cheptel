<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Saillie extends Model
{
    protected $fillable = [
        'cycle_reproduction_id',
        'date_heure',
        'type',
        'verrat_id',
        'semence_lot_numero',
        'intervenant',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_heure' => 'datetime',
        ];
    }

    public function cycleReproduction(): BelongsTo
    {
        return $this->belongsTo(CycleReproduction::class);
    }

    public function verrat(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'verrat_id');
    }
}
