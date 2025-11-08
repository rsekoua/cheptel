<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    protected $fillable = [
        'numero_lot',
        'type_lot',
        'date_creation',
        'nb_animaux_depart',
        'nb_animaux_actuel',
        'poids_total_depart_kg',
        'poids_moyen_depart_kg',
        'poids_total_actuel_kg',
        'poids_moyen_actuel_kg',
        'date_derniere_pesee',
        'salle_id',
        'statut_lot',
        'plan_alimentation_id',
        'date_sortie',
        'nb_animaux_sortie',
        'poids_total_sortie_kg',
        'poids_moyen_sortie_kg',
        'prix_vente_total',
        'destination_sortie',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_creation' => 'date',
            'poids_total_depart_kg' => 'decimal:2',
            'poids_moyen_depart_kg' => 'decimal:2',
            'poids_total_actuel_kg' => 'decimal:2',
            'poids_moyen_actuel_kg' => 'decimal:2',
            'date_derniere_pesee' => 'date',
            'date_sortie' => 'date',
            'poids_total_sortie_kg' => 'decimal:2',
            'poids_moyen_sortie_kg' => 'decimal:2',
            'prix_vente_total' => 'decimal:2',
        ];
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function planAlimentation(): BelongsTo
    {
        return $this->belongsTo(PlanAlimentation::class);
    }

    public function portees(): BelongsToMany
    {
        return $this->belongsToMany(Portee::class, 'lot_portee')
            ->withPivot('nb_porcelets_transferes', 'poids_total_transfere_kg')
            ->withTimestamps();
    }

    public function porteesDestination(): HasMany
    {
        return $this->hasMany(Portee::class, 'lot_destination_id');
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

    public function scopeActifs(Builder $query): void
    {
        $query->where('statut_lot', 'actif');
    }

    public function scopePostSevrage(Builder $query): void
    {
        $query->where('type_lot', 'post_sevrage');
    }

    public function scopeEngraissement(Builder $query): void
    {
        $query->where('type_lot', 'engraissement');
    }

    protected function tauxMortalite(): Attribute
    {
        return Attribute::make(
            get: function (): ?float {
                if ($this->nb_animaux_depart === 0) {
                    return null;
                }

                $mortalite = $this->nb_animaux_depart - $this->nb_animaux_actuel;

                return round(($mortalite / $this->nb_animaux_depart) * 100, 2);
            }
        );
    }

    protected function gmq(): Attribute
    {
        return Attribute::make(
            get: function (): ?float {
                if (! $this->poids_moyen_depart_kg || ! $this->poids_moyen_actuel_kg) {
                    return null;
                }

                $joursEcoules = $this->date_creation->diffInDays(now());

                if ($joursEcoules === 0) {
                    return null;
                }

                $gainTotal = ($this->poids_moyen_actuel_kg - $this->poids_moyen_depart_kg) * 1000;

                return round($gainTotal / $joursEcoules, 0);
            }
        );
    }
}
