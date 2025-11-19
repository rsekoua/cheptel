<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'animaux';

    protected $fillable = [
        'numero_identification',
        'type_animal',
        'race_id',
        'date_naissance',
        'date_entree',
        'is_actif',
        'origine',
        'numero_mere',
        'numero_pere',
        'provenance',
        'date_reforme',
        'motif_reforme',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'date_entree' => 'date',
            'date_reforme' => 'date',
            'is_admin' => 'boolean',
        ];
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
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
        return $this->hasMany(EvenementsSanitaire::class);
    }

    public function evenementsAlimentation(): HasMany
    {
        return $this->hasMany(EvenementsAlimentation::class);
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

    public function scopeVerrats(Builder $query): void
    {
        $query->where('type_animal', 'verrat');
    }

    public function scopeGestantes(Builder $query): void
    {
        $query->where('statut_actuel', 'gestante');
    }

    public function scopeEnLactation(Builder $query): void
    {
        $query->where('statut_actuel', 'en_lactation');
    }

    public function scopeActifs(Builder $query): void
    {
        $query->where('statut_actuel', 'active');
    }

    public function scopeSevrees(Builder $query): void
    {
        $query->where('statut_actuel', 'sevree');
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
