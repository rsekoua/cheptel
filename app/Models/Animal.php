<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use SoftDeletes;

    protected $table = 'animaux';

    protected $fillable = [
        'numero_identification',
        'type_animal',
        'race_id',
        'sexe',
        'date_naissance',
        'date_entree',
        'origine',
        'numero_mere',
        'numero_pere',
        'statut_actuel',
        'salle_id',
        'place_numero',
        'poids_actuel_kg',
        'date_derniere_pesee',
        'plan_alimentation_id',
        'bande',
        'date_reforme',
        'motif_reforme',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'date_entree' => 'date',
            'poids_actuel_kg' => 'decimal:2',
            'date_derniere_pesee' => 'date',
            'date_reforme' => 'date',
        ];
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function planAlimentation(): BelongsTo
    {
        return $this->belongsTo(PlanAlimentation::class);
    }

    public function cyclesReproduction(): HasMany
    {
        return $this->hasMany(CycleReproduction::class);
    }

    public function portees(): HasMany
    {
        return $this->hasMany(Portee::class);
    }

    public function sailliesCommeVerrat(): HasMany
    {
        return $this->hasMany(Saillie::class, 'verrat_id');
    }

    public function evenementsSanitaires(): HasMany
    {
        return $this->hasMany(EvenementSanitaire::class);
    }

    public function evenementsAlimentation(): HasMany
    {
        return $this->hasMany(EvenementAlimentation::class);
    }

    public function pesees(): HasMany
    {
        return $this->hasMany(Pesee::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class);
    }

    public function taches(): HasMany
    {
        return $this->hasMany(Tache::class);
    }

    public function scopeTruies(Builder $query): void
    {
        $query->where('type_animal', 'truie');
    }

    public function scopeCochettes(Builder $query): void
    {
        $query->where('type_animal', 'cochette');
    }

    public function scopeVerrats(Builder $query): void
    {
        $query->where('type_animal', 'verrat');
    }

    public function scopeGestantes(Builder $query): void
    {
        $query->whereIn('statut_actuel', ['gestante_attente', 'gestante_confirmee']);
    }

    public function scopeEnLactation(Builder $query): void
    {
        $query->where('statut_actuel', 'en_lactation');
    }

    public function scopeActifs(Builder $query): void
    {
        $query->whereNotIn('statut_actuel', ['reforme', 'retraite']);
    }

    public function scopeBande(Builder $query, string $bande): void
    {
        $query->where('bande', $bande);
    }

    public function age(): int
    {
        return $this->date_naissance?->diffInDays(now()) ?? 0;
    }

    public function ageEnMois(): int
    {
        return (int) floor($this->age() / 30);
    }
}
