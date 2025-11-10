<?php

use App\Filament\Resources\CycleReproductions\Pages\EditCycleReproduction;
use App\Models\Animal;
use App\Models\CycleReproduction;
use App\Models\Race;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('rejects diagnostic date when no saillie exists', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'race_id' => $race->id,
    ]);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 1,
        'statut_cycle' => 'en_cours',
    ]);

    // Essayer de définir une date de diagnostic sans aucune saillie
    Livewire::test(EditCycleReproduction::class, ['record' => $cycle->id])
        ->fillForm([
            'date_diagnostic' => now()->format('Y-m-d'),
        ])
        ->call('save')
        ->assertHasFormErrors(['date_diagnostic']);
});

it('rejects diagnostic date when it is before first saillie', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'race_id' => $race->id,
    ]);

    $datePremiereSaillie = now()->subDays(10);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 1,
        'statut_cycle' => 'en_cours',
    ]);

    // Créer une saillie
    $cycle->saillies()->create([
        'cycle_reproduction_id' => $cycle->id,
        'type' => 'IA',
        'date_heure' => $datePremiereSaillie,
        'semence_lot_numero' => 'LOT-123',
    ]);

    // Essayer de définir une date de diagnostic AVANT la première saillie
    $dateDiagnosticInvalide = $datePremiereSaillie->copy()->subDays(1);

    Livewire::test(EditCycleReproduction::class, ['record' => $cycle->id])
        ->fillForm([
            'date_diagnostic' => $dateDiagnosticInvalide->format('Y-m-d'),
            'saillies' => [
                [
                    'type' => 'IA',
                    'date_heure' => $datePremiereSaillie->format('Y-m-d H:i:s'),
                    'semence_lot_numero' => 'LOT-123',
                ],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['date_diagnostic']);
});

it('rejects diagnostic date equal to first saillie date', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'race_id' => $race->id,
    ]);

    $datePremiereSaillie = now()->subDays(10);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 1,
        'statut_cycle' => 'en_cours',
    ]);

    $cycle->saillies()->create([
        'cycle_reproduction_id' => $cycle->id,
        'type' => 'IA',
        'date_heure' => $datePremiereSaillie,
        'semence_lot_numero' => 'LOT-123',
    ]);

    // Date de diagnostic égale à la date de la saillie (invalide)
    Livewire::test(EditCycleReproduction::class, ['record' => $cycle->id])
        ->fillForm([
            'date_diagnostic' => $datePremiereSaillie->format('Y-m-d'),
            'saillies' => [
                [
                    'type' => 'IA',
                    'date_heure' => $datePremiereSaillie->format('Y-m-d H:i:s'),
                    'semence_lot_numero' => 'LOT-123',
                ],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['date_diagnostic']);
});

it('accepts valid diagnostic date after first saillie', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'race_id' => $race->id,
    ]);

    $datePremiereSaillie = now()->subDays(25);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 1,
        'statut_cycle' => 'en_cours',
    ]);

    $cycle->saillies()->create([
        'cycle_reproduction_id' => $cycle->id,
        'type' => 'IA',
        'date_heure' => $datePremiereSaillie,
        'semence_lot_numero' => 'LOT-123',
    ]);

    // Date de diagnostic 21 jours après la saillie (valide)
    $dateDiagnosticValide = $datePremiereSaillie->copy()->addDays(21);

    Livewire::test(EditCycleReproduction::class, ['record' => $cycle->id])
        ->fillForm([
            'date_diagnostic' => $dateDiagnosticValide->format('Y-m-d'),
            'saillies' => [
                [
                    'type' => 'IA',
                    'date_heure' => $datePremiereSaillie->format('Y-m-d H:i:s'),
                    'semence_lot_numero' => 'LOT-123',
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $cycle->refresh();
    expect($cycle->date_diagnostic->format('Y-m-d'))->toBe($dateDiagnosticValide->format('Y-m-d'));
});

it('uses earliest saillie date when multiple saillies exist', function () {
    $race = Race::factory()->create();

    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'sexe' => 'F',
        'race_id' => $race->id,
    ]);

    $premiereSaillie = now()->subDays(25);
    $deuxiemeSaillie = $premiereSaillie->copy()->addHours(12);
    $troisiemeSaillie = $premiereSaillie->copy()->addDays(1);

    $cycle = CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 1,
        'statut_cycle' => 'en_cours',
    ]);

    $cycle->saillies()->create([
        'cycle_reproduction_id' => $cycle->id,
        'type' => 'IA',
        'date_heure' => $premiereSaillie,
        'semence_lot_numero' => 'LOT-123',
    ]);

    $cycle->saillies()->create([
        'cycle_reproduction_id' => $cycle->id,
        'type' => 'IA',
        'date_heure' => $deuxiemeSaillie,
        'semence_lot_numero' => 'LOT-124',
    ]);

    $cycle->saillies()->create([
        'cycle_reproduction_id' => $cycle->id,
        'type' => 'IA',
        'date_heure' => $troisiemeSaillie,
        'semence_lot_numero' => 'LOT-125',
    ]);

    // Date de diagnostic entre la deuxième et troisième saillie (devrait être invalide car < première saillie + 21 jours minimum)
    $dateDiagnostic = $premiereSaillie->copy()->addDays(21);

    Livewire::test(EditCycleReproduction::class, ['record' => $cycle->id])
        ->fillForm([
            'date_diagnostic' => $dateDiagnostic->format('Y-m-d'),
            'saillies' => [
                [
                    'type' => 'IA',
                    'date_heure' => $premiereSaillie->format('Y-m-d H:i:s'),
                    'semence_lot_numero' => 'LOT-123',
                ],
                [
                    'type' => 'IA',
                    'date_heure' => $deuxiemeSaillie->format('Y-m-d H:i:s'),
                    'semence_lot_numero' => 'LOT-124',
                ],
                [
                    'type' => 'IA',
                    'date_heure' => $troisiemeSaillie->format('Y-m-d H:i:s'),
                    'semence_lot_numero' => 'LOT-125',
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $cycle->refresh();
    expect($cycle->date_diagnostic->format('Y-m-d'))->toBe($dateDiagnostic->format('Y-m-d'));
});
