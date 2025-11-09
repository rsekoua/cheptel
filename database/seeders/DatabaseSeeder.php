<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\CycleReproduction;
use App\Models\Lot;
use App\Models\Portee;
use App\Models\Race;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur de test
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'rsekoua@local.host',
            'password' => hash::make('password'),
            ]);

        // Créer des races de porcs avec des noms uniques
        $races = collect([
            ['nom' => 'Large White', 'type' => 'maternelle', 'gmq_moyen' => 750, 'poids_adulte_moyen' => 280],
            ['nom' => 'Landrace', 'type' => 'maternelle', 'gmq_moyen' => 720, 'poids_adulte_moyen' => 270],
            ['nom' => 'Duroc', 'type' => 'paternelle', 'gmq_moyen' => 800, 'poids_adulte_moyen' => 300],
            ['nom' => 'Piétrain', 'type' => 'paternelle', 'gmq_moyen' => 680, 'poids_adulte_moyen' => 260],
            ['nom' => 'Hampshire', 'type' => 'mixte', 'gmq_moyen' => 740, 'poids_adulte_moyen' => 275],
        ])->map(fn ($race) => Race::create($race));

        // Créer des animaux (truies, cochettes, verrats)
        $truies = Animal::factory(6)->create([
            'type_animal' => 'truie',
            'sexe' => 'F',
            'race_id' => fn () => $races->random()->id,
            //'statut_actuel' => 'gestante_confirmee',
        ]);

        $cochettes = Animal::factory(2)->create([
            'type_animal' => 'cochette',
            'sexe' => 'F',
            'race_id' => fn () => $races->random()->id,
            'statut_actuel' => 'en_chaleurs',
        ]);

        $verrats = Animal::factory(3)->create([
            'type_animal' => 'verrat',
            'race_id' => fn () => $races->random()->id,
            'sexe' => 'M',
            'statut_actuel' => 'active',
        ]);

        // Créer des lots de production
        $lots = Lot::factory(8)->create([
            'statut_lot' => 'actif',
        ]);

        // Créer des cycles de reproduction pour quelques truies
        $cycles = collect();
        $truies->take(6)->each(function ($truie) use (&$cycles) {
            $cycle = CycleReproduction::factory()->create([
                'animal_id' => $truie->id,
                'numero_cycle' => 1,
                'statut_cycle' => 'en_cours',
                'resultat_diagnostic' => 'positif',
            ]);
            $cycles->push($cycle);
        });

        // Créer des portées pour les cycles avec diagnostic positif
        $cycles->where('resultat_diagnostic', 'positif')->take(4)->each(function ($cycle) use ($lots) {
            $portee = Portee::factory()->create([
                'cycle_reproduction_id' => $cycle->id,
                'animal_id' => $cycle->animal_id,
                'nb_nes_vifs' => rand(10, 14),
                'nb_mort_nes' => rand(0, 2),
                'nb_momifies' => rand(0, 1),
                'nb_sevres' => rand(8, 12),
                'lot_destination_id' => $lots->random()->id,
            ]);

            // Certaines portées sont réparties dans plusieurs lots (méthode avancée)
            if (rand(0, 1)) {
                $nbLots = rand(2, 3);
                $nbSevresTotalRestant = $portee->nb_sevres;

                // Sélectionner des lots uniques (différents de lot_destination_id)
                $lotsDisponibles = $lots->where('id', '!=', $portee->lot_destination_id)->shuffle();

                for ($i = 0; $i < min($nbLots, $lotsDisponibles->count()) && $nbSevresTotalRestant > 0; $i++) {
                    $nbPorcelets = min(rand(2, 5), $nbSevresTotalRestant);
                    $poidsTotal = $nbPorcelets * rand(60, 80) / 10; // 6-8 kg par porcelet

                    $portee->lots()->attach($lotsDisponibles[$i]->id, [
                        'nb_porcelets_transferes' => $nbPorcelets,
                        'poids_total_transfere_kg' => $poidsTotal,
                    ]);

                    $nbSevresTotalRestant -= $nbPorcelets;
                }
            }
        });
    }
}
