<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockAliment>
 */
class StockAlimentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombreSacs = fake()->randomFloat(2, 10, 100);
        $poidsSacStandard = 50;
        $poidsKg = $nombreSacs * $poidsSacStandard;
        $coutMoyenKg = fake()->randomFloat(2, 200, 500);
        $valeurStock = $poidsKg * $coutMoyenKg;

        return [
            'aliment_id' => \App\Models\Aliment::factory(),
            'type_stock' => fake()->randomElement(['entrepot', 'preparation']),
            'nombre_sacs_disponibles' => $nombreSacs,
            'poids_kg_disponible' => $poidsKg,
            'cout_moyen_kg' => $coutMoyenKg,
            'valeur_stock' => $valeurStock,
            'derniere_maj' => now(),
        ];
    }
}
