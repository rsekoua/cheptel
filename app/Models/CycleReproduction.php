<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CycleReproduction extends Model
{
    protected $table = 'cycles_reproduction';

    protected $fillable = [
        'animal_id',
        'numero_cycle',
        'date_debut',
        'date_chaleurs',
        'date_premiere_saillie',
        'type_saillie',
        'date_diagnostic',
        'resultat_diagnostic',
        'date_mise_bas_prevue',
        'date_mise_bas_reelle',
        'statut_cycle',
        'motif_echec',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_chaleurs' => 'datetime',
            'date_premiere_saillie' => 'datetime',
            'date_diagnostic' => 'date',
            'date_mise_bas_prevue' => 'date',
            'date_mise_bas_reelle' => 'date',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function saillies(): HasMany
    {
        return $this->hasMany(Saillie::class);
    }

    public function portee(): HasOne
    {
        return $this->hasOne(Portee::class);
    }

    public function scopeEnCours(Builder $query): void
    {
        $query->where('statut_cycle', 'en_cours');
    }

    public function scopeGestantes(Builder $query): void
    {
        $query->where('resultat_diagnostic', 'positif')
            ->where('statut_cycle', 'en_cours');
    }

    public function scopeTermines(Builder $query): void
    {
        $query->whereIn('statut_cycle', ['termine_succes', 'termine_echec', 'avorte']);
    }

    public function jourDeGestation(): ?int
    {
        if (! $this->date_premiere_saillie) {
            return null;
        }

        return $this->date_premiere_saillie->diffInDays(now());
    }
}
