<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aliment extends Model
{
    /** @use HasFactory<\Database\Factories\AlimentFactory> */
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'poids_sac_standard',
        'seuil_alerte',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'poids_sac_standard' => 'decimal:2',
            'seuil_alerte' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    public function stocks()
    {
        return $this->hasMany(StockAliment::class);
    }

    public function stockEntrepot()
    {
        return $this->hasOne(StockAliment::class)->where('type_stock', 'entrepot');
    }

    public function stockPreparation()
    {
        return $this->hasOne(StockAliment::class)->where('type_stock', 'preparation');
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementAliment::class);
    }

    public function detailsPreparations()
    {
        return $this->hasMany(DetailPreparation::class);
    }
}
