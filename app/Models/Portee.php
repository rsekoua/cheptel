<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Portee extends Model
{
    protected $fillable = [
        'cycle_reproduction_id',
        'animal_id',
        'date_mise_bas',
        'nb_nes_vifs',
        'nb_mort_nes',
        'nb_momifies',
        'poids_moyen_naissance_g',
        'date_sevrage',
        'nb_sevres',
        'poids_total_sevrage_kg',
        'poids_moyen_sevrage_kg',
        'lot_destination_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_mise_bas' => 'datetime',
            'date_sevrage' => 'date',
            'poids_total_sevrage_kg' => 'decimal:2',
            'poids_moyen_sevrage_kg' => 'decimal:2',
        ];
    }

    public function cycleReproduction(): BelongsTo
    {
        return $this->belongsTo(CycleReproduction::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function lotDestination(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'lot_destination_id');
    }

    public function lots(): BelongsToMany
    {
        return $this->belongsToMany(Lot::class, 'lot_portee')
            ->withPivot('nb_porcelets_transferes', 'poids_total_transfere_kg')
            ->withTimestamps();
    }

    public function taches(): HasMany
    {
        return $this->hasMany(Tache::class);
    }

    public function scopeSevrees(Builder $query): void
    {
        $query->whereNotNull('date_sevrage');
    }

    public function scopeNonSevrees(Builder $query): void
    {
        $query->whereNull('date_sevrage');
    }

    protected function tauxMortaliteMaternite(): Attribute
    {
        return Attribute::make(
            get: function (): ?float {
                if ($this->nb_nes_vifs === 0) {
                    return null;
                }

                $mortalite = $this->nb_nes_vifs - ($this->nb_sevres ?? 0);

                return round(($mortalite / $this->nb_nes_vifs) * 100, 2);
            }
        );
    }
}
