<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salle extends Model
{
    protected $fillable = [
        'type_salle_id',
        'nom',
        'capacite',
        'statut',
        'date_debut_vide_sanitaire',
        'duree_vide_sanitaire_jours',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_debut_vide_sanitaire' => 'date',
        ];
    }

    public function typeSalle(): BelongsTo
    {
        return $this->belongsTo(TypeSalle::class);
    }

    public function animaux(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class, 'salle_destination_id');
    }

    public function taches(): HasMany
    {
        return $this->hasMany(Tache::class);
    }

    public function scopeDisponibles(Builder $query): void
    {
        $query->where('statut', 'disponible');
    }

    public function scopeOccupees(Builder $query): void
    {
        $query->where('statut', 'occupee');
    }
}
