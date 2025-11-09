<?php

use App\Models\Animal;
use App\Models\CycleReproduction;
use App\Models\Lot;
use App\Models\Portee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->actingAs($this->user);
});

test('portee can be assigned to a single lot via lot_destination_id', function () {
    // Arrange: Create a truie, cycle, and lot
    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'statut_actuel' => 'gestante_confirmee',
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'resultat_diagnostic' => 'positif',
    ]);

    $lot = Lot::factory()->create([
        'numero_lot' => 'LOT-001',
        'statut_lot' => 'actif',
    ]);

    // Act: Create a portee with lot_destination_id
    $portee = Portee::create([
        'cycle_reproduction_id' => $cycle->id,
        'animal_id' => $truie->id,
        'date_mise_bas' => now(),
        'nb_nes_vifs' => 12,
        'nb_mort_nes' => 1,
        'nb_momifies' => 0,
        // nb_total est calculé automatiquement
        'lot_destination_id' => $lot->id,
    ]);

    // Assert
    expect($portee->lot_destination_id)->toBe($lot->id);
    expect($portee->lotDestination->numero_lot)->toBe('LOT-001');

    // When using lot_destination_id (simple method), the portee appears via the BelongsTo relationship
    expect($portee->lotDestination->id)->toBe($lot->id);

    // Note: The many-to-many relationship (lots()) is separate from lot_destination_id
    // If we want the lot to appear in both, we'd need to manually sync it
});

test('portee can be distributed across multiple lots using pivot table', function () {
    // Arrange: Create a truie, cycle, and multiple lots
    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'statut_actuel' => 'gestante_confirmee',
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'resultat_diagnostic' => 'positif',
    ]);

    $lot1 = Lot::factory()->create(['numero_lot' => 'LOT-001', 'statut_lot' => 'actif']);
    $lot2 = Lot::factory()->create(['numero_lot' => 'LOT-002', 'statut_lot' => 'actif']);
    $lot3 = Lot::factory()->create(['numero_lot' => 'LOT-003', 'statut_lot' => 'actif']);

    // Act: Create a portee and distribute across lots
    $portee = Portee::create([
        'cycle_reproduction_id' => $cycle->id,
        'animal_id' => $truie->id,
        'date_mise_bas' => now(),
        'nb_nes_vifs' => 12,
        'nb_mort_nes' => 1,
        'nb_momifies' => 0,
        // nb_total est calculé automatiquement
        'date_sevrage' => now()->addDays(28),
        'nb_sevres' => 10,
    ]);

    // Distribute sevrés across 3 lots
    $portee->lots()->attach($lot1->id, [
        'nb_porcelets_transferes' => 4,
        'poids_total_transfere_kg' => 28.5,
    ]);

    $portee->lots()->attach($lot2->id, [
        'nb_porcelets_transferes' => 3,
        'poids_total_transfere_kg' => 19.2,
    ]);

    $portee->lots()->attach($lot3->id, [
        'nb_porcelets_transferes' => 3,
        'poids_total_transfere_kg' => 18.8,
    ]);

    // Assert
    expect($portee->lots()->count())->toBe(3);

    // Verify pivot data for lot1
    $pivot1 = $portee->lots()->where('lots.id', $lot1->id)->first()->pivot;
    expect($pivot1->nb_porcelets_transferes)->toBe(4);
    expect($pivot1->poids_total_transfere_kg)->toBe(28.5);

    // Verify each lot can see the portee
    expect($lot1->portees()->count())->toBe(1);
    expect($lot2->portees()->count())->toBe(1);
    expect($lot3->portees()->count())->toBe(1);

    // Total transferred should equal nb_sevres
    $totalTransfered = $portee->lots->sum(fn ($lot) => $lot->pivot->nb_porcelets_transferes);
    expect($totalTransfered)->toBe(10);
    expect($totalTransfered)->toBe($portee->nb_sevres);
});

test('portee can use both methods simultaneously', function () {
    // Arrange: Create resources
    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'statut_actuel' => 'gestante_confirmee',
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'resultat_diagnostic' => 'positif',
    ]);

    $lotDestination = Lot::factory()->create(['numero_lot' => 'LOT-DEST', 'statut_lot' => 'actif']);
    $lotPartiel = Lot::factory()->create(['numero_lot' => 'LOT-PARTIEL', 'statut_lot' => 'actif']);

    // Act: Create portee with lot_destination_id
    $portee = Portee::create([
        'cycle_reproduction_id' => $cycle->id,
        'animal_id' => $truie->id,
        'date_mise_bas' => now(),
        'nb_nes_vifs' => 12,
        'nb_mort_nes' => 1,
        'nb_momifies' => 0,
        'nb_total' => 13,
        'lot_destination_id' => $lotDestination->id,
        'date_sevrage' => now()->addDays(28),
        'nb_sevres' => 10,
    ]);

    // Also attach to additional lots via pivot
    $portee->lots()->attach($lotPartiel->id, [
        'nb_porcelets_transferes' => 2,
        'poids_total_transfere_kg' => 14.5,
    ]);

    // Assert: Both relationships work independently
    expect($portee->lot_destination_id)->toBe($lotDestination->id);
    expect($portee->lotDestination->numero_lot)->toBe('LOT-DEST');

    // The many-to-many relationship only includes lots explicitly attached via pivot
    expect($portee->lots()->count())->toBe(1); // Only lotPartiel (manually attached)
    expect($portee->lots->first()->numero_lot)->toBe('LOT-PARTIEL');

    // The lot_destination is accessed via the BelongsTo relationship, not the many-to-many
    expect($lotPartiel->portees()->count())->toBe(1);
});
