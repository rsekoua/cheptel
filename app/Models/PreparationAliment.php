<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreparationAliment extends Model
{
    /** @use HasFactory<\Database\Factories\PreparationAlimentFactory> */
    use HasFactory;

    protected $fillable = [
        'nom',
        'quantite_totale',
        'cout_total',
        'date_preparation',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'quantite_totale' => 'decimal:2',
            'cout_total' => 'decimal:2',
            'date_preparation' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(DetailPreparation::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementAliment::class);
    }
}
