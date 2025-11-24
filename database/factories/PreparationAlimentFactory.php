<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PreparationAliment>
 */
class PreparationAlimentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantiteTotale = fake()->randomFloat(2, 50, 500);
        $coutTotal = fake()->randomFloat(2, 10000, 100000);

        return [
            'nom' => fake()->optional()->randomElement(['Ration du matin', 'Ration du soir', 'Préparation spéciale']),
            'quantite_totale' => $quantiteTotale,
            'cout_total' => $coutTotal,
            'date_preparation' => fake()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->optional()->sentence(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
