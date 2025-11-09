<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Portee>
 */
class PorteeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nbNesVifs = fake()->numberBetween(8, 14);
        $nbMortNes = fake()->numberBetween(0, 3);
        $nbMomifies = fake()->numberBetween(0, 2);
        $nbSevres = fake()->numberBetween(max(0, $nbNesVifs - 3), $nbNesVifs);

        $dateMiseBas = fake()->dateTimeBetween('-60 days', '-28 days');
        $dateSevrage = (clone $dateMiseBas)->modify('+'.rand(21, 28).' days');

        return [
            'cycle_reproduction_id' => \App\Models\CycleReproduction::factory(),
            'animal_id' => \App\Models\Animal::factory(),
            'date_mise_bas' => $dateMiseBas,
            'nb_nes_vifs' => $nbNesVifs,
            'nb_mort_nes' => $nbMortNes,
            'nb_momifies' => $nbMomifies,
            // nb_total est calculé automatiquement par la base de données
            'poids_moyen_naissance_g' => fake()->optional()->numberBetween(1200, 1600),
            'date_sevrage' => $dateSevrage,
            'nb_sevres' => $nbSevres,
            'poids_total_sevrage_kg' => $nbSevres > 0 ? $nbSevres * fake()->randomFloat(2, 6, 8) : null,
            'poids_moyen_sevrage_kg' => fake()->optional()->randomFloat(2, 6, 8),
            'lot_destination_id' => null,
        ];
    }
}
