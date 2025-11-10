<?php

use App\Models\Animal;
use App\Models\CycleReproduction;
use App\Models\Race;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates new cycle when sow is weaned', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    // Créer un cycle existant terminé avec succès
    CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 5,
        'statut_cycle' => 'termine_succes',
    ]);

    // Vérifier qu'il n'y a qu'un seul cycle
    expect(CycleReproduction::where('animal_id', $truie->id)->count())->toBe(1);

    // Changer le statut vers sevree → Doit créer un nouveau cycle
    $truie->update(['statut_actuel' => 'sevree']);

    // Vérifier qu'un nouveau cycle #6 a été créé
    expect(CycleReproduction::where('animal_id', $truie->id)->count())->toBe(2);

    $nouveauCycle = CycleReproduction::where('animal_id', $truie->id)
        ->orderBy('numero_cycle', 'desc')
        ->first();

    expect($nouveauCycle->numero_cycle)->toBe(6)
        ->and($nouveauCycle->statut_cycle)->toBe('en_cours')
        ->and($nouveauCycle->resultat_diagnostic)->toBe('en_attente');
});

it('creates first cycle for gilt in first heat', function () {
    $race = Race::factory()->create();

    $cochette = Animal::factory()->create([
        'type_animal' => 'cochette',
        'sexe' => 'F',
        'statut_actuel' => 'active',
        'race_id' => $race->id,
    ]);

    // Aucun cycle existant
    expect(CycleReproduction::where('animal_id', $cochette->id)->count())->toBe(0);

    // Premières chaleurs → Doit créer un cycle
    $cochette->update(['statut_actuel' => 'en_chaleurs']);

    // Vérifier qu'un cycle #1 a été créé
    $cycle = CycleReproduction::where('animal_id', $cochette->id)->first();

    expect($cycle)->not->toBeNull()
        ->and($cycle->numero_cycle)->toBe(1)
        ->and($cycle->statut_cycle)->toBe('en_cours');
});

it('does not create cycle if one is already in progress', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'gestante_confirmee',
        'race_id' => $race->id,
    ]);

    // Créer un cycle EN COURS
    CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 3,
        'statut_cycle' => 'en_cours',
    ]);

    // Tenter de changer le statut vers sevree
    $truie->update(['statut_actuel' => 'sevree']);

    // Vérifier qu'AUCUN nouveau cycle n'a été créé
    expect(CycleReproduction::where('animal_id', $truie->id)->count())->toBe(1);

    $cycle = CycleReproduction::where('animal_id', $truie->id)->first();
    expect($cycle->numero_cycle)->toBe(3)
        ->and($cycle->statut_cycle)->toBe('en_cours');
});

it('creates new cycle when returning to heat after failed cycle', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'gestante_attente',
        'race_id' => $race->id,
    ]);

    // Créer un cycle terminé en échec (diagnostic négatif)
    CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 2,
        'statut_cycle' => 'termine_echec',
        'resultat_diagnostic' => 'negatif',
    ]);

    // La truie revient en chaleurs → Doit créer un nouveau cycle
    $truie->update(['statut_actuel' => 'en_chaleurs']);

    // Vérifier qu'un nouveau cycle #3 a été créé
    expect(CycleReproduction::where('animal_id', $truie->id)->count())->toBe(2);

    $nouveauCycle = CycleReproduction::where('animal_id', $truie->id)
        ->orderBy('numero_cycle', 'desc')
        ->first();

    expect($nouveauCycle->numero_cycle)->toBe(3)
        ->and($nouveauCycle->statut_cycle)->toBe('en_cours');
});

it('does not create cycle for gilt already having cycles when going to heat', function () {
    $race = Race::factory()->create();

    $cochette = Animal::factory()->create([
        'type_animal' => 'cochette',
        'sexe' => 'F',
        'statut_actuel' => 'gestante_confirmee',
        'race_id' => $race->id,
    ]);

    // La cochette a déjà eu un cycle (ce n'est plus sa première chaleur)
    CycleReproduction::factory()->create([
        'animal_id' => $cochette->id,
        'numero_cycle' => 1,
        'statut_cycle' => 'en_cours',
    ]);

    // Tenter de passer en chaleurs (simulation d'une erreur de statut)
    $cochette->update(['statut_actuel' => 'en_chaleurs']);

    // Vérifier qu'AUCUN nouveau cycle n'a été créé
    // (car le premier cycle est toujours en_cours)
    expect(CycleReproduction::where('animal_id', $cochette->id)->count())->toBe(1);
});

it('does not create cycle when status changes to other values', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_chaleurs',
        'race_id' => $race->id,
    ]);

    // Changements de statut qui ne doivent PAS créer de cycle
    $statutsTests = [
        'gestante_attente',
        'gestante_confirmee',
        'en_lactation',
        'reforme',
    ];

    foreach ($statutsTests as $statut) {
        $truie->update(['statut_actuel' => $statut]);

        expect(CycleReproduction::where('animal_id', $truie->id)->count())->toBe(0);
    }
});

it('increments cycle number correctly across multiple cycles', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    // Premier cycle terminé
    CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 1,
        'statut_cycle' => 'termine_succes',
    ]);

    // Sevrer → crée cycle #2
    $truie->update(['statut_actuel' => 'sevree']);

    expect(CycleReproduction::where('animal_id', $truie->id)->count())->toBe(2);

    // Terminer le cycle #2
    $cycle2 = CycleReproduction::where('animal_id', $truie->id)
        ->where('numero_cycle', 2)
        ->first();
    $cycle2->update(['statut_cycle' => 'termine_succes']);

    // Sevrer à nouveau → crée cycle #3
    $truie->update(['statut_actuel' => 'en_lactation']);
    $truie->update(['statut_actuel' => 'sevree']);

    expect(CycleReproduction::where('animal_id', $truie->id)->count())->toBe(3);

    // Vérifier que les numéros sont corrects
    $cycles = CycleReproduction::where('animal_id', $truie->id)
        ->orderBy('numero_cycle')
        ->pluck('numero_cycle')
        ->toArray();

    expect($cycles)->toBe([1, 2, 3]);
});

it('creates cycle with correct initial data', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'statut_actuel' => 'en_lactation',
        'race_id' => $race->id,
    ]);

    // Sevrer pour créer un cycle
    $truie->update(['statut_actuel' => 'sevree']);

    $cycle = CycleReproduction::where('animal_id', $truie->id)->first();

    expect($cycle)->not->toBeNull()
        ->and($cycle->numero_cycle)->toBe(1)
        ->and($cycle->statut_cycle)->toBe('en_cours')
        ->and($cycle->resultat_diagnostic)->toBe('en_attente')
        ->and($cycle->date_debut)->not->toBeNull()
        ->and($cycle->date_chaleurs)->toBeNull()
        ->and($cycle->date_premiere_saillie)->toBeNull();
});
