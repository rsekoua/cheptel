<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProduitSanitaire extends Model
{
    protected $fillable = [
        'nom',
        'type',
        'laboratoire',
        'principe_actif',
        'numero_amm',
        'delai_attente_jours',
        'voie_administration',
        'dosage_ml_kg',
        'stock_actuel',
        'stock_alerte',
    ];

    protected function casts(): array
    {
        return [
            'dosage_ml_kg' => 'decimal:3',
        ];
    }

    public function evenementsSanitaires(): HasMany
    {
        return $this->hasMany(EvenementsSanitaire::class);
    }

    public function scopeEnRupture(Builder $query): void
    {
        $query->where('stock_actuel', '<=', 'stock_alerte');
    }

    public function scopeVaccins(Builder $query): void
    {
        $query->where('type', 'vaccin');
    }
}
