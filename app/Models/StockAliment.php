<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAliment extends Model
{
    /** @use HasFactory<\Database\Factories\StockAlimentFactory> */
    use HasFactory;

    protected $fillable = [
        'aliment_id',
        'type_stock',
        'nombre_sacs_disponibles',
        'poids_kg_disponible',
        'cout_moyen_kg',
        'valeur_stock',
        'derniere_maj',
    ];

    protected function casts(): array
    {
        return [
            'nombre_sacs_disponibles' => 'decimal:2',
            'poids_kg_disponible' => 'decimal:2',
            'cout_moyen_kg' => 'decimal:2',
            'valeur_stock' => 'decimal:2',
            'derniere_maj' => 'datetime',
        ];
    }

    public function aliment()
    {
        return $this->belongsTo(Aliment::class);
    }
}
