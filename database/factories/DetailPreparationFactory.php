<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailPreparation>
 */
class DetailPreparationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantiteKg = fake()->randomFloat(2, 10, 100);
        $coutUnitaireKg = fake()->randomFloat(2, 200, 500);
        $valeur = $quantiteKg * $coutUnitaireKg;

        return [
            'preparation_aliment_id' => \App\Models\PreparationAliment::factory(),
            'aliment_id' => \App\Models\Aliment::factory(),
            'quantite_kg' => $quantiteKg,
            'cout_unitaire_kg' => $coutUnitaireKg,
            'valeur' => $valeur,
        ];
    }
}
