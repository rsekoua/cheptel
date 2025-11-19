<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CycleReproduction extends Model
{
    use HasFactory;

    protected $table = 'cycles_reproduction';

    protected $fillable = [
        'animal_id',
        'numero_cycle',
        'date_debut',
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

    public function scopeGestationEnCours(Builder $query): void
    {
        $query->where('statut_cycle', 'gestation_en_cours');
    }

    public function scopeGestantes(Builder $query): void
    {
        $query->where('resultat_diagnostic', 'positif')
            ->where('statut_cycle', 'gestation_en_cours');
    }

    public function scopeMiseBasEffectuee(Builder $query): void
    {
        $query->where('statut_cycle', 'mise_bas_effectuee');
    }

    public function scopeEchecGestation(Builder $query): void
    {
        $query->where('statut_cycle', 'echec_gestation');
    }

    public function jourDeGestation(): ?int
    {
        $premiereSaillie = $this->saillies()->orderBy('date_heure')->first();

        if (! $premiereSaillie) {
            return null;
        }

        return $premiereSaillie->date_heure->diffInDays(now());
    }

    public function datePremiereSaillie(): ?\Carbon\Carbon
    {
        $premiereSaillie = $this->saillies()->orderBy('date_heure')->first();

        return $premiereSaillie?->date_heure;
    }

    public function nombreSaillies(): int
    {
        return $this->saillies()->count();
    }
}
