<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lot>
 */
class LotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nbAnimauxDepart = fake()->numberBetween(50, 200);
        $poidsTotal = $nbAnimauxDepart * fake()->randomFloat(2, 6, 8);

        return [
            'numero_lot' => 'LOT-'.fake()->unique()->numberBetween(1000, 9999),
            'type_lot' => fake()->randomElement(['post_sevrage', 'engraissement']),
            'date_creation' => fake()->dateTimeBetween('-6 months', 'now'),
            'statut_lot' => fake()->randomElement(['actif', 'termine', 'vide']),
            'nb_animaux_depart' => $nbAnimauxDepart,
            'poids_total_depart_kg' => $poidsTotal,
            'poids_moyen_depart_kg' => $poidsTotal / $nbAnimauxDepart,
            'nb_animaux_actuel' => $nbAnimauxDepart,
            'poids_total_actuel_kg' => $poidsTotal,
            'poids_moyen_actuel_kg' => $poidsTotal / $nbAnimauxDepart,
            'salle_id' => null,
            'plan_alimentation_id' => null,
        ];
    }
}
