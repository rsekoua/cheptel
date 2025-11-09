<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Race>
 */
class RaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->randomElement(['Large White', 'Landrace', 'Duroc', 'Piétrain', 'Hampshire']),
            'description' => fake()->optional()->sentence(),
            'type' => fake()->randomElement(['maternelle', 'paternelle', 'mixte']),
            'gmq_moyen' => fake()->optional()->randomFloat(2, 600, 900),
            'poids_adulte_moyen' => fake()->optional()->randomFloat(2, 200, 350),
        ];
    }
}
