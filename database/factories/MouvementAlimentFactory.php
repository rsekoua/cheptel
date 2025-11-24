<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MouvementAliment>
 */
class MouvementAlimentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $typeMouvement = fake()->randomElement(['achat', 'transfert_entree', 'transfert_sortie', 'sortie_preparation']);
        $typeStock = fake()->randomElement(['entrepot', 'preparation']);
        $nombreSacs = fake()->randomFloat(2, 1, 50);
        $poidsKg = $nombreSacs * 50;
        $coutUnitaireKg = fake()->randomFloat(2, 200, 500);
        $valeurMouvement = $poidsKg * $coutUnitaireKg;

        $data = [
            'aliment_id' => \App\Models\Aliment::factory(),
            'type_mouvement' => $typeMouvement,
            'type_stock' => $typeStock,
            'preparation_aliment_id' => null,
            'nombre_sacs' => $nombreSacs,
            'poids_kg' => $poidsKg,
            'prix_unitaire_sac' => null,
            'cout_total' => null,
            'cout_unitaire_kg' => $coutUnitaireKg,
            'valeur_mouvement' => $valeurMouvement,
            'date_mouvement' => fake()->dateTimeBetween('-1 year', 'now'),
            'reference' => fake()->optional()->numerify('REF-####'),
            'notes' => fake()->optional()->sentence(),
            'user_id' => \App\Models\User::factory(),
        ];

        if ($typeMouvement === 'achat') {
            $prixUnitaireSac = fake()->randomFloat(2, 10000, 25000);
            $data['prix_unitaire_sac'] = $prixUnitaireSac;
            $data['cout_total'] = $nombreSacs * $prixUnitaireSac;
        }

        if ($typeMouvement === 'sortie_preparation') {
            $data['preparation_aliment_id'] = \App\Models\PreparationAliment::factory();
        }

        return $data;
    }
}
