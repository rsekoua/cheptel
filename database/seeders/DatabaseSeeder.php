<?php

namespace Database\Seeders;

use App\Models\Aliment;
use App\Models\Animal;
use App\Models\CycleReproduction;
use App\Models\DetailPreparation;
use App\Models\Lot;
use App\Models\MouvementAliment;
use App\Models\Portee;
use App\Models\PreparationAliment;
use App\Models\Race;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur de test
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@local.host',
            'password' => hash::make('password'),
        ]);

        // Créer des races de porcs avec des noms uniques
        $races = collect([
            ['nom' => 'Large White', 'type' => 'maternelle', 'gmq_moyen' => 750, 'poids_adulte_moyen' => 280],
            ['nom' => 'Landrace', 'type' => 'maternelle', 'gmq_moyen' => 720, 'poids_adulte_moyen' => 270],
            ['nom' => 'Duroc', 'type' => 'paternelle', 'gmq_moyen' => 800, 'poids_adulte_moyen' => 300],
        ])->map(fn ($race) => Race::create($race));

        // Créer des animaux (truies, cochettes, verrats)
        $truies = Animal::factory(8)->create([
            'type_animal' => 'truie',
            //   'sexe' => 'F',
            'race_id' => fn () => $races->random()->id,
            // 'statut_actuel' => 'gestante_confirmee',
        ]);

        $verrats = Animal::factory(2)->create([
            'type_animal' => 'verrat',
            'race_id' => fn () => $races->random()->id,
            //  'sexe' => 'M',
            // 'is_actif' => 'active',
        ]);

        // Créer des lots de production
        //        $lots = Lot::factory(8)->create([
        //            'statut_lot' => 'actif',
        //        ]);

        // Créer des cycles de reproduction pour quelques truies
        //        $cycles = collect();
        //        $truies->take(6)->each(function ($truie) use (&$cycles) {
        //            $cycle = CycleReproduction::factory()->create([
        //                'animal_id' => $truie->id,
        //                'numero_cycle' => 1,
        //                'statut_cycle' => 'en_cours',
        //                'resultat_diagnostic' => 'positif',
        //            ]);
        //            $cycles->push($cycle);
        //        });

        // Créer des portées pour les cycles avec diagnostic positif
        //        $cycles->where('resultat_diagnostic', 'positif')->take(4)->each(function ($cycle) use ($lots) {
        //            $portee = Portee::factory()->create([
        //                'cycle_reproduction_id' => $cycle->id,
        //                'animal_id' => $cycle->animal_id,
        //                'nb_nes_vifs' => rand(10, 14),
        //                'nb_mort_nes' => rand(0, 2),
        //                'nb_momifies' => rand(0, 1),
        //                'nb_sevres' => rand(8, 12),
        //                'lot_destination_id' => $lots->random()->id,
        //            ]);
        //
        //            // Certaines portées sont réparties dans plusieurs lots (méthode avancée)
        // //            if (rand(0, 1)) {
        // //                $nbLots = rand(2, 3);
        // //                $nbSevresTotalRestant = $portee->nb_sevres;
        // //
        // //                // Sélectionner des lots uniques (différents de lot_destination_id)
        // //                $lotsDisponibles = $lots->where('id', '!=', $portee->lot_destination_id)->shuffle();
        // //
        // //                for ($i = 0; $i < min($nbLots, $lotsDisponibles->count()) && $nbSevresTotalRestant > 0; $i++) {
        // //                    $nbPorcelets = min(rand(2, 5), $nbSevresTotalRestant);
        // //                    $poidsTotal = $nbPorcelets * rand(60, 80) / 10; // 6-8 kg par porcelet
        // //
        // //                    $portee->lots()->attach($lotsDisponibles[$i]->id, [
        // //                        'nb_porcelets_transferes' => $nbPorcelets,
        // //                        'poids_total_transfere_kg' => $poidsTotal,
        // //                    ]);
        // //
        // //                    $nbSevresTotalRestant -= $nbPorcelets;
        // //                }
        // //            }
        //        });

        // === GESTION DES ALIMENTS ===

        // Créer des aliments avec des noms uniques
        $aliments = collect([
            ['nom' => 'Maïs grain', 'poids' => 50, 'seuil' => 500, 'description' => 'Céréale riche en énergie'],
            ['nom' => 'Tourteau de soja', 'poids' => 40, 'seuil' => 400, 'description' => 'Source de protéines'],
            ['nom' => 'Son de blé', 'poids' => 30, 'seuil' => 300, 'description' => 'Riche en fibres'],
            ['nom' => 'CMV (Complément Minéral Vitaminé)', 'poids' => 25, 'seuil' => 200, 'description' => 'Vitamines et minéraux'],
            ['nom' => 'Farine de poisson', 'poids' => 25, 'seuil' => 250, 'description' => 'Protéines animales'],
        ])->map(fn ($data) => Aliment::create([
            'nom' => $data['nom'],
            'description' => $data['description'],
            'poids_sac_standard' => $data['poids'],
            'seuil_alerte' => $data['seuil'],
            'actif' => true,
        ]));

        $user = User::first();

        // Créer des achats pour chaque aliment
        $aliments->each(function ($aliment) use ($user) {
            $nombreSacs = rand(10, 30);
            $poidsSac = $aliment->poids_sac_standard;
            $prixSac = rand(8000, 15000);

            MouvementAliment::create([
                'aliment_id' => $aliment->id,
                'type_mouvement' => 'achat',
                'type_stock' => 'entrepot',
                'nombre_sacs' => $nombreSacs,
                'poids_kg' => $nombreSacs * $poidsSac,
                'prix_unitaire_sac' => $prixSac,
                'cout_total' => $nombreSacs * $prixSac,
                'date_mouvement' => now()->subDays(rand(1, 10)),
                'reference' => 'ACH-'.rand(1000, 9999),
                'user_id' => $user->id,
            ]);
        });

        // Créer quelques transferts vers la zone de préparation
        $aliments->random(3)->each(function ($aliment) use ($user) {
            $poidsTransfert = rand(100, 300);

            MouvementAliment::create([
                'aliment_id' => $aliment->id,
                'type_mouvement' => 'transfert_entree',
                'type_stock' => 'preparation',
                'nombre_sacs' => 0,
                'poids_kg' => $poidsTransfert,
                'date_mouvement' => now()->subDays(rand(1, 5)),
                'notes' => 'Transfert pour préparation',
                'user_id' => $user->id,
            ]);
        });

        // Créer une préparation d'aliment composé
        $preparation = PreparationAliment::create([
            'nom' => 'Aliment porcelets démarrage',
            'date_preparation' => now()->subDays(2),
            'notes' => 'Formule spéciale pour porcelets sevrés',
            'user_id' => $user->id,
        ]);

        // Ajouter les détails de la préparation
        $stockEntrepot = \App\Models\StockAliment::where('type_stock', 'entrepot')->get();

        if ($stockEntrepot->count() >= 3) {
            collect([
                ['aliment' => $stockEntrepot[0]->aliment, 'quantite' => 50],
                ['aliment' => $stockEntrepot[1]->aliment, 'quantite' => 30],
                ['aliment' => $stockEntrepot[3]->aliment, 'quantite' => 5],
            ])->each(function ($detail) use ($preparation) {
                $stock = \App\Models\StockAliment::where('aliment_id', $detail['aliment']->id)
                    ->where('type_stock', 'entrepot')
                    ->first();

                if ($stock && $stock->cout_moyen_kg > 0) {
                    DetailPreparation::create([
                        'preparation_aliment_id' => $preparation->id,
                        'aliment_id' => $detail['aliment']->id,
                        'quantite_kg' => $detail['quantite'],
                        'cout_unitaire_kg' => $stock->cout_moyen_kg,
                    ]);
                }
            });
        }
    }
}
