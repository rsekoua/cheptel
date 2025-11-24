<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aliment>
 */
class AlimentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $aliments = [
            ['nom' => 'Maïs', 'poids' => 50, 'seuil' => 500],
            ['nom' => 'Soja', 'poids' => 40, 'seuil' => 400],
            ['nom' => 'Tourteau', 'poids' => 25, 'seuil' => 300],
            ['nom' => 'CMV (Complément Minéral Vitaminé)', 'poids' => 20, 'seuil' => 200],
            ['nom' => 'Son de blé', 'poids' => 30, 'seuil' => 350],
            ['nom' => 'Farine de poisson', 'poids' => 25, 'seuil' => 250],
        ];

        $aliment = fake()->randomElement($aliments);

        return [
            'nom' => $aliment['nom'],
            'description' => fake()->optional()->sentence(),
            'poids_sac_standard' => $aliment['poids'],
            'seuil_alerte' => $aliment['seuil'],
            'actif' => true,
        ];
    }
}
