<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPreparation extends Model
{
    /** @use HasFactory<\Database\Factories\DetailPreparationFactory> */
    use HasFactory;

    protected $fillable = [
        'preparation_aliment_id',
        'aliment_id',
        'quantite_kg',
        'cout_unitaire_kg',
        'valeur',
    ];

    protected function casts(): array
    {
        return [
            'quantite_kg' => 'decimal:2',
            'cout_unitaire_kg' => 'decimal:2',
            'valeur' => 'decimal:2',
        ];
    }

    public function preparation()
    {
        return $this->belongsTo(PreparationAliment::class, 'preparation_aliment_id');
    }

    public function aliment()
    {
        return $this->belongsTo(Aliment::class);
    }
}
