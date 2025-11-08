<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tache extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'type_tache',
        'priorite',
        'type_cible',
        'animal_id',
        'lot_id',
        'portee_id',
        'salle_id',
        'date_echeance',
        'date_debut_periode',
        'statut',
        'date_realisation',
        'utilisateur_assigne_id',
        'utilisateur_realisation_id',
        'generee_automatiquement',
        'evenement_lie_type',
        'evenement_lie_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_echeance' => 'date',
            'date_debut_periode' => 'date',
            'date_realisation' => 'datetime',
            'generee_automatiquement' => 'boolean',
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

    public function portee(): BelongsTo
    {
        return $this->belongsTo(Portee::class);
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function utilisateurAssigne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_assigne_id');
    }

    public function utilisateurRealisation(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_realisation_id');
    }

    public function scopeEnAttente(Builder $query): void
    {
        $query->where('statut', 'en_attente');
    }

    public function scopeEnRetard(Builder $query): void
    {
        $query->where('statut', '!=', 'terminee')
            ->where('date_echeance', '<', now());
    }

    public function scopeCritiques(Builder $query): void
    {
        $query->where('priorite', 'critique');
    }

    public function scopePourAujourdHui(Builder $query): void
    {
        $query->where('date_echeance', '<=', now()->endOfDay())
            ->where('statut', '!=', 'terminee');
    }
}
