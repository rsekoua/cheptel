<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Animal>
 */
class AnimalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_identification' => 'AN-'.fake()->unique()->numberBetween(1000, 9999),
            'type_animal' => fake()->randomElement(['truie', 'cochette', 'verrat']),
            'race_id' => \App\Models\Race::factory(),
            'sexe' => fake()->randomElement(['F', 'M']),
            'date_naissance' => fake()->dateTimeBetween('-3 years', '-6 months'),
            'origine' => fake()->randomElement(['naissance_elevage', 'achat_externe']),
            'statut_actuel' => fake()->randomElement(['sevree', 'en_chaleurs', 'gestante_attente', 'gestante_confirmee', 'en_lactation', 'reforme', 'active', 'retraite']),
            'salle_id' => null,
            'plan_alimentation_id' => null,
        ];
    }
}
