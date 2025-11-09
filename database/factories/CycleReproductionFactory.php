<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CycleReproduction>
 */
class CycleReproductionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateDebut = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'animal_id' => \App\Models\Animal::factory(),
            'numero_cycle' => fake()->numberBetween(1, 10),
            'date_debut' => $dateDebut,
            'statut_cycle' => fake()->randomElement(['en_cours', 'termine_succes', 'termine_echec', 'avorte']),
            'resultat_diagnostic' => fake()->randomElement(['en_attente', 'positif', 'negatif']),
            'date_diagnostic' => fake()->optional()->dateTimeBetween($dateDebut, 'now'),
            'date_mise_bas_prevue' => fake()->optional()->dateTimeBetween($dateDebut, '+120 days'),
            'date_mise_bas_reelle' => null,
        ];
    }
}
