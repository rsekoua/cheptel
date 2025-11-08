<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanAlimentation extends Model
{
    protected $fillable = [
        'nom',
        'type_animal',
        'description',
        'energie_mcal_jour',
        'proteine_pourcent',
        'ration_kg_jour',
        'a_volonte',
    ];

    protected function casts(): array
    {
        return [
            'energie_mcal_jour' => 'decimal:2',
            'proteine_pourcent' => 'decimal:1',
            'ration_kg_jour' => 'decimal:2',
            'a_volonte' => 'boolean',
        ];
    }

    public function animaux(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function evenementsAlimentation(): HasMany
    {
        return $this->hasMany(EvenementAlimentation::class);
    }

    public function scopeReproducteurs(Builder $query): void
    {
        $query->where('type_animal', 'reproducteur');
    }

    public function scopeProduction(Builder $query): void
    {
        $query->where('type_animal', 'production');
    }
}
