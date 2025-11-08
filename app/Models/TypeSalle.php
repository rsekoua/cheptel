<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeSalle extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'capacite_type',
        'temperature_optimale',
    ];

    protected function casts(): array
    {
        return [
            'temperature_optimale' => 'decimal:1',
        ];
    }

    public function salles(): HasMany
    {
        return $this->hasMany(Salle::class);
    }
}
