<?php

use App\Models\Animal;
use App\Models\CycleReproduction;
use App\Models\Lot;
use App\Models\Portee;
use App\Models\Race;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('can display litters ready for weaning', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'statut_cycle' => 'en_cours',
    ]);

    Portee::factory()->create([
        'cycle_reproduction_id' => $cycle->id,
        'animal_id' => $truie->id,
        'date_sevrage' => null,
        'nb_nes_vifs' => 12,
        'nb_sevres' => null,
    ]);

    Livewire::test(\App\Filament\Resources\Portees\Pages\ListPortees::class)
        ->assertSuccessful();
});

it('can wean a single litter to a new lot', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'statut_cycle' => 'en_cours',
        'numero_cycle' => 1,
    ]);

    $portee = Portee::factory()->create([
        'cycle_reproduction_id' => $cycle->id,
        'animal_id' => $truie->id,
        'date_sevrage' => null,
        'nb_nes_vifs' => 12,
        'nb_sevres' => null,
        'poids_total_sevrage_kg' => null,
    ]);

    $dateSevrage = now()->toDateString();
    $nbSevres = 11;
    $poidsTotal = 77.0;

    Livewire::test(\App\Filament\Resources\Portees\Pages\ListPortees::class)
        ->callTableBulkAction('sevrer', [$portee], data: [
            'date_sevrage' => $dateSevrage,
            'mode_lot' => 'nouveau',
            'nouveau_lot_numero' => 'LOT-PS-TEST-001',
            'donnees_portees' => [
                [
                    'portee_id' => $portee->id,
                    'truie' => $truie->numero_identification,
                    'nb_sevres' => $nbSevres,
                    'poids_total_kg' => $poidsTotal,
                ],
            ],
        ])
        ->assertNotified();

    $portee->refresh();
    $truie->refresh();
    $cycle->refresh();

    expect($portee->date_sevrage)->not->toBeNull()
        ->and($portee->nb_sevres)->toBe($nbSevres)
        ->and($portee->poids_total_sevrage_kg)->toEqual($poidsTotal)
        ->and($portee->lot_destination_id)->not->toBeNull();

    expect($truie->statut_actuel)->toBe('sevree');
    expect($cycle->statut_cycle)->toBe('termine_succes');

    $lot = Lot::where('numero_lot', 'LOT-PS-TEST-001')->first();
    expect($lot)->not->toBeNull()
        ->and($lot->type_lot)->toBe('post_sevrage')
        ->and($lot->nb_animaux_depart)->toBe($nbSevres)
        ->and($lot->nb_animaux_actuel)->toBe($nbSevres)
        ->and($lot->poids_total_depart_kg)->toEqual($poidsTotal)
        ->and($lot->statut_lot)->toBe('actif');
});

it('can wean multiple litters to a new lot', function () {
    $race = Race::factory()->create();

    $truie1 = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    $truie2 = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    $cycle1 = CycleReproduction::factory()->create([
        'animal_id' => $truie1->id,
        'statut_cycle' => 'en_cours',
    ]);

    $cycle2 = CycleReproduction::factory()->create([
        'animal_id' => $truie2->id,
        'statut_cycle' => 'en_cours',
    ]);

    $portee1 = Portee::factory()->create([
        'cycle_reproduction_id' => $cycle1->id,
        'animal_id' => $truie1->id,
        'date_sevrage' => null,
        'nb_nes_vifs' => 12,
        'nb_sevres' => null,
    ]);

    $portee2 = Portee::factory()->create([
        'cycle_reproduction_id' => $cycle2->id,
        'animal_id' => $truie2->id,
        'date_sevrage' => null,
        'nb_nes_vifs' => 11,
        'nb_sevres' => null,
    ]);

    $dateSevrage = now()->toDateString();

    Livewire::test(\App\Filament\Resources\Portees\Pages\ListPortees::class)
        ->callTableBulkAction('sevrer', [$portee1, $portee2], data: [
            'date_sevrage' => $dateSevrage,
            'mode_lot' => 'nouveau',
            'nouveau_lot_numero' => 'LOT-PS-MULTI-001',
            'donnees_portees' => [
                [
                    'portee_id' => $portee1->id,
                    'truie' => $truie1->numero_identification,
                    'nb_sevres' => 11,
                    'poids_total_kg' => 77.0,
                ],
                [
                    'portee_id' => $portee2->id,
                    'truie' => $truie2->numero_identification,
                    'nb_sevres' => 10,
                    'poids_total_kg' => 70.0,
                ],
            ],
        ])
        ->assertNotified();

    $lot = Lot::where('numero_lot', 'LOT-PS-MULTI-001')->first();

    expect($lot)->not->toBeNull()
        ->and($lot->nb_animaux_depart)->toBe(21)
        ->and($lot->poids_total_depart_kg)->toEqual(147.0);

    $portee1->refresh();
    $portee2->refresh();

    expect($portee1->lot_destination_id)->toBe($lot->id)
        ->and($portee2->lot_destination_id)->toBe($lot->id);

    expect($portee1->lots()->count())->toBe(1)
        ->and($portee2->lots()->count())->toBe(1);
});

it('can wean litters to an existing lot', function () {
    $race = Race::factory()->create();

    $lotExistant = Lot::factory()->create([
        'numero_lot' => 'LOT-EXISTANT-001',
        'type_lot' => 'post_sevrage',
        'statut_lot' => 'actif',
        'nb_animaux_depart' => 50,
        'nb_animaux_actuel' => 50,
        'poids_total_depart_kg' => 350.0,
        'poids_moyen_depart_kg' => 7.0,
        'poids_total_actuel_kg' => 350.0,
        'poids_moyen_actuel_kg' => 7.0,
    ]);

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'statut_cycle' => 'en_cours',
    ]);

    $portee = Portee::factory()->create([
        'cycle_reproduction_id' => $cycle->id,
        'animal_id' => $truie->id,
        'date_sevrage' => null,
        'nb_nes_vifs' => 12,
        'nb_sevres' => null,
    ]);

    $dateSevrage = now()->toDateString();
    $nbSevres = 11;
    $poidsTotal = 77.0;

    Livewire::test(\App\Filament\Resources\Portees\Pages\ListPortees::class)
        ->callTableBulkAction('sevrer', [$portee], data: [
            'date_sevrage' => $dateSevrage,
            'mode_lot' => 'existant',
            'lot_existant_id' => $lotExistant->id,
            'donnees_portees' => [
                [
                    'portee_id' => $portee->id,
                    'truie' => $truie->numero_identification,
                    'nb_sevres' => $nbSevres,
                    'poids_total_kg' => $poidsTotal,
                ],
            ],
        ])
        ->assertNotified();

    $lotExistant->refresh();

    expect($lotExistant->nb_animaux_actuel)->toBe(61)
        ->and($lotExistant->poids_total_actuel_kg)->toEqual(427.0);

    $portee->refresh();

    expect($portee->lot_destination_id)->toBe($lotExistant->id);
});

it('prevents weaning an already weaned litter', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'sevree',
        'race_id' => $race->id,
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'statut_cycle' => 'termine_succes',
    ]);

    $portee = Portee::factory()->create([
        'cycle_reproduction_id' => $cycle->id,
        'animal_id' => $truie->id,
        'date_sevrage' => now()->subDays(7),
        'nb_nes_vifs' => 12,
        'nb_sevres' => 11,
        'poids_total_sevrage_kg' => 77.0,
    ]);

    Livewire::test(\App\Filament\Resources\Portees\Pages\ListPortees::class)
        ->callTableBulkAction('sevrer', [$portee], data: [
            'date_sevrage' => now()->toDateString(),
            'mode_lot' => 'nouveau',
            'nouveau_lot_numero' => 'LOT-PS-DUPE-001',
            'donnees_portees' => [
                [
                    'portee_id' => $portee->id,
                    'truie' => $truie->numero_identification,
                    'nb_sevres' => 11,
                    'poids_total_kg' => 77.0,
                ],
            ],
        ]);

    expect(Lot::where('numero_lot', 'LOT-PS-DUPE-001')->exists())->toBeFalse();
});

it('records litter-lot relationship in pivot table', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'statut_cycle' => 'en_cours',
    ]);

    $portee = Portee::factory()->create([
        'cycle_reproduction_id' => $cycle->id,
        'animal_id' => $truie->id,
        'date_sevrage' => null,
        'nb_nes_vifs' => 12,
        'nb_sevres' => null,
    ]);

    $dateSevrage = now()->toDateString();
    $nbSevres = 11;
    $poidsTotal = 77.0;

    Livewire::test(\App\Filament\Resources\Portees\Pages\ListPortees::class)
        ->callTableBulkAction('sevrer', [$portee], data: [
            'date_sevrage' => $dateSevrage,
            'mode_lot' => 'nouveau',
            'nouveau_lot_numero' => 'LOT-PS-PIVOT-001',
            'donnees_portees' => [
                [
                    'portee_id' => $portee->id,
                    'truie' => $truie->numero_identification,
                    'nb_sevres' => $nbSevres,
                    'poids_total_kg' => $poidsTotal,
                ],
            ],
        ])
        ->assertNotified();

    $portee->refresh();

    expect($portee->lots)->toHaveCount(1);

    $pivotData = $portee->lots->first()->pivot;

    expect($pivotData->nb_porcelets_transferes)->toBe($nbSevres)
        ->and($pivotData->poids_total_transfere_kg)->toEqual($poidsTotal);
});

it('ensures lot totals equal sum of all litters', function () {
    $race = Race::factory()->create();

    $portees = collect();

    // Créer 3 portées avec des données connues
    for ($i = 0; $i < 3; $i++) {
        $truie = Animal::factory()->create([
            'type_animal' => 'truie',
            'sexe' => 'F',
            'statut_actuel' => 'en_lactation',
            'race_id' => $race->id,
        ]);

        $cycle = CycleReproduction::factory()->create([
            'animal_id' => $truie->id,
            'statut_cycle' => 'en_cours',
        ]);

        $portee = Portee::factory()->create([
            'cycle_reproduction_id' => $cycle->id,
            'animal_id' => $truie->id,
            'date_sevrage' => null,
            'nb_nes_vifs' => 12,
            'nb_sevres' => null,
        ]);

        $portees->push([
            'portee' => $portee,
            'truie' => $truie,
            'nb_sevres' => 10 + $i, // 10, 11, 12
            'poids_total' => 70.0 + ($i * 7), // 70, 77, 84
        ]);
    }

    // Calculer les totaux attendus
    $totalPorceletsAttendu = $portees->sum('nb_sevres'); // 10 + 11 + 12 = 33
    $totalPoidsAttendu = $portees->sum('poids_total'); // 70 + 77 + 84 = 231

    $donneesPortees = $portees->map(fn ($p) => [
        'portee_id' => $p['portee']->id,
        'truie' => $p['truie']->numero_identification,
        'nb_sevres' => $p['nb_sevres'],
        'poids_total_kg' => $p['poids_total'],
    ])->toArray();

    Livewire::test(\App\Filament\Resources\Portees\Pages\ListPortees::class)
        ->callTableBulkAction('sevrer', $portees->pluck('portee')->all(), data: [
            'date_sevrage' => now()->toDateString(),
            'mode_lot' => 'nouveau',
            'nouveau_lot_numero' => 'LOT-PS-TOTAL-001',
            'donnees_portees' => $donneesPortees,
        ])
        ->assertNotified();

    $lot = Lot::where('numero_lot', 'LOT-PS-TOTAL-001')->first();

    // VÉRIFICATION CRUCIALE : Les totaux du lot = EXACTEMENT la somme des portées
    expect($lot)->not->toBeNull();

    // Nombre d'animaux du lot = somme des nb_sevres
    expect($lot->nb_animaux_depart)->toBe($totalPorceletsAttendu)
        ->and($lot->nb_animaux_actuel)->toBe($totalPorceletsAttendu);

    // Poids total du lot = somme des poids_total_sevrage_kg
    expect($lot->poids_total_depart_kg)->toEqual($totalPoidsAttendu)
        ->and($lot->poids_total_actuel_kg)->toEqual($totalPoidsAttendu);

    // Poids moyen = total poids / total porcelets
    $poidsMoyenAttendu = $totalPoidsAttendu / $totalPorceletsAttendu;
    expect($lot->poids_moyen_depart_kg)->toBeCloseTo($poidsMoyenAttendu, 0.01)
        ->and($lot->poids_moyen_actuel_kg)->toBeCloseTo($poidsMoyenAttendu, 0.01);

    // Vérifier également dans la table pivot
    $totalPivot = $lot->portees()
        ->sum('lot_portee.nb_porcelets_transferes');

    expect($totalPivot)->toBe($totalPorceletsAttendu);

    $totalPoidsPivot = $lot->portees()
        ->sum('lot_portee.poids_total_transfere_kg');

    expect($totalPoidsPivot)->toEqual($totalPoidsAttendu);
});
