<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'type',
        'gmq_moyen',
        'poids_adulte_moyen',
    ];

    protected function casts(): array
    {
        return [
            'gmq_moyen' => 'decimal:2',
            'poids_adulte_moyen' => 'decimal:2',
        ];
    }

    public function animaux(): HasMany
    {
        return $this->hasMany(Animal::class);
    }
}
