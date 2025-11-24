<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementAliment extends Model
{
    /** @use HasFactory<\Database\Factories\MouvementAlimentFactory> */
    use HasFactory;

    protected $fillable = [
        'aliment_id',
        'type_mouvement',
        'type_stock',
        'preparation_aliment_id',
        'nombre_sacs',
        'poids_kg',
        'prix_unitaire_sac',
        'cout_total',
        'cout_unitaire_kg',
        'valeur_mouvement',
        'date_mouvement',
        'reference',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'nombre_sacs' => 'decimal:2',
            'poids_kg' => 'decimal:2',
            'prix_unitaire_sac' => 'decimal:2',
            'cout_total' => 'decimal:2',
            'cout_unitaire_kg' => 'decimal:2',
            'valeur_mouvement' => 'decimal:2',
            'date_mouvement' => 'date',
        ];
    }

    public function aliment()
    {
        return $this->belongsTo(Aliment::class);
    }

    public function preparation()
    {
        return $this->belongsTo(PreparationAliment::class, 'preparation_aliment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
