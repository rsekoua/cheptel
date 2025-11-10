# Gestion automatique des cycles de reproduction

## Vue d'ensemble

Votre application utilise un **Observer** Laravel (`AnimalObserver`) qui surveille les changements de statut des animaux et **crée automatiquement de nouveaux cycles de reproduction** lorsque c'est nécessaire.

C'est un système très élégant qui permet de gérer le **cycle perpétuel** de reproduction des truies sans intervention manuelle !

## Comment ça fonctionne ?

### 1. L'Observer surveille les modifications

```php
// app/Observers/AnimalObserver.php
public function updated(Animal $animal): void
{
    // Vérifier si le statut a changé
    if (!$animal->isDirty('statut_actuel')) {
        return; // Si le statut n'a pas changé, on ne fait rien
    }

    // ... logique de création de cycle
}
```

**Déclencheur** : Chaque fois qu'un `Animal` est modifié, l'observer est notifié.

### 2. Déclencheurs de création automatique

L'observer crée un nouveau cycle dans **DEUX situations** :

#### **Situation A : Truie/Cochette sevrée** 🐷

```php
'sevree' => in_array($animal->type_animal, ['truie', 'cochette'])
```

**Quand ?** Après le sevrage d'une portée (dans notre action `SevrerPorteesAction`)

**Pourquoi ?** Une truie qui vient de sevrer sa portée va naturellement revenir en chaleurs dans 3-7 jours et être prête pour un nouveau cycle de reproduction.

**Exemple de flux** :
```
Truie #123 → Statut: en_lactation
    ↓
[SEVRAGE] Action SevrerPorteesAction
    ↓
Truie #123 → Statut: sevree ⚡ DÉCLENCHEUR !
    ↓
Observer détecte le changement
    ↓
Création automatique du Cycle #6
    ↓
Cycle #6 → statut: en_cours, résultat_diagnostic: en_attente
```

#### **Situation B : Première chaleur d'une cochette** 🐖

```php
'en_chaleurs' => $animal->type_animal === 'cochette'
                 && $this->estPremiereChaleur($animal)
```

**Quand ?** Une jeune cochette (truie nullipare) atteint sa puberté et a ses premières chaleurs

**Pourquoi ?** C'est le début de sa carrière reproductive

**Exemple de flux** :
```
Cochette #456 → Statut: active (jeune femelle en croissance)
    ↓
[DÉTECTION CHALEURS] L'éleveur observe les chaleurs
    ↓
Cochette #456 → Statut: en_chaleurs ⚡ DÉCLENCHEUR !
    ↓
Observer vérifie : aucun cycle existant pour cet animal
    ↓
Création automatique du Cycle #1 (premier cycle)
```

### 3. Création du nouveau cycle

```php
protected function creerNouveauCycle(Animal $animal): void
{
    // 1. Calculer le numéro de cycle
    $dernierCycle = CycleReproduction::where('animal_id', $animal->id)
        ->orderBy('numero_cycle', 'desc')
        ->first();

    $numeroCycle = $dernierCycle ? $dernierCycle->numero_cycle + 1 : 1;

    // 2. Créer le nouveau cycle
    CycleReproduction::create([
        'animal_id' => $animal->id,
        'numero_cycle' => $numeroCycle,    // Incrémenté automatiquement !
        'date_debut' => now(),              // Date actuelle
        'statut_cycle' => 'en_cours',       // Cycle actif
        'resultat_diagnostic' => 'en_attente', // Pas encore diagnostiqué
    ]);
}
```

**Ce qui est créé automatiquement** :
- ✅ `numero_cycle` : Incrémenté automatiquement (1, 2, 3, 4...)
- ✅ `date_debut` : Date du jour
- ✅ `statut_cycle` : `en_cours`
- ✅ `resultat_diagnostic` : `en_attente`

**Ce qui reste à remplir manuellement** :
- ⏳ `date_chaleurs` : Quand l'éleveur observe les chaleurs
- ⏳ `date_premiere_saillie` : Quand la saillie/insémination est faite
- ⏳ `date_diagnostic` : Quand le diagnostic de gestation est réalisé
- ⏳ `date_mise_bas_prevue` : Calculée après diagnostic positif
- ⏳ `date_mise_bas_reelle` : Enregistrée à la mise-bas

## Cycle de vie complet d'une truie

Voici le **cycle perpétuel** géré automatiquement :

```
┌─────────────────────────────────────────────────────────┐
│                  CYCLE DE VIE D'UNE TRUIE               │
└─────────────────────────────────────────────────────────┘

1. SEVRAGE (Jour 0)
   Statut: sevree → Observer crée Cycle #N automatiquement ⚡

2. CHALEURS (Jour 3-7)
   Statut: en_chaleurs
   Action manuelle: Enregistrer la date de chaleurs

3. SAILLIE/INSÉMINATION (Jour 3-7)
   Action manuelle: Enregistrer la saillie
   Cycle: date_premiere_saillie remplie

4. ATTENTE DIAGNOSTIC (Jour 21-28)
   Statut: gestante_attente

5. DIAGNOSTIC (Jour 21-28)
   Action manuelle: Enregistrer résultat diagnostic
   Si positif → Statut: gestante_confirmee
   Si négatif → Cycle termine_echec, retour aux chaleurs

6. GESTATION (Jour 28-114)
   Statut: gestante_confirmee
   Attente de 114 jours (3 mois, 3 semaines, 3 jours)

7. MISE-BAS (Jour 114)
   Action manuelle: Enregistrer la portée
   Statut: en_lactation
   Cycle: date_mise_bas_reelle remplie

8. LACTATION (21-28 jours)
   Statut: en_lactation
   Les porcelets tètent

9. SEVRAGE → Retour à l'étape 1 ! 🔄
   Statut: sevree → Observer crée Cycle #(N+1) automatiquement ⚡
```

## Enregistrement dans l'application

L'observer est enregistré dans `AppServiceProvider.php` :

```php
public function boot(): void
{
    Animal::observe(AnimalObserver::class);
}
```

Cela signifie que **chaque fois qu'un Animal est créé, modifié ou supprimé**, Laravel appelle automatiquement les méthodes correspondantes de l'observer.

## Avantages de ce système

### ✅ Automatisation intelligente
- Pas besoin de créer manuellement un cycle après chaque sevrage
- Le numéro de cycle s'incrémente automatiquement
- Moins d'erreurs humaines

### ✅ Cohérence des données
- Un cycle est toujours créé au bon moment
- Le statut de l'animal et l'existence d'un cycle sont synchronisés
- La date de début est toujours correcte

### ✅ Traçabilité complète
- Chaque truie a un historique complet de tous ses cycles
- On peut voir combien de cycles une truie a effectués
- On peut calculer des statistiques par cycle

## Cas particuliers

### Cochette vs Truie

```php
// Cochette (nullipare) : premier cycle créé aux premières chaleurs
if ($animal->type_animal === 'cochette' && !CycleReproduction::exists()) {
    // Créer Cycle #1
}

// Truie (multipare) : nouveau cycle créé après chaque sevrage
if (in_array($animal->type_animal, ['truie', 'cochette']) && $statut === 'sevree') {
    // Créer Cycle #(N+1)
}
```

### Cycle échoué

Si un cycle échoue (diagnostic négatif, avortement), le statut du cycle passe à `termine_echec` mais **un nouveau cycle n'est PAS créé automatiquement**.

Pourquoi ? Parce que la truie va simplement revenir en chaleurs, et l'utilisateur mettra à jour le statut vers `en_chaleurs`, ce qui ne déclenche PAS la création d'un cycle (seulement pour les cochettes en première chaleur).

**Solution** : L'utilisateur doit créer manuellement un nouveau cycle OU attendre le prochain sevrage.

### Réforme d'un animal

Quand un animal est réformé (`statut_actuel = 'reforme'`), aucun nouveau cycle n'est créé. C'est voulu : l'animal quitte l'élevage.

## Schéma récapitulatif

```
┌──────────────────────────────────────────────────────────────┐
│                    ANIMAL OBSERVER                           │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  Event: Animal.updated                                       │
│    ↓                                                         │
│  Vérification: statut_actuel a changé ?                     │
│    ↓ OUI                                                     │
│  Match sur nouveau statut:                                   │
│    ↓                                                         │
│  ┌─────────────────┐         ┌──────────────────┐          │
│  │ statut: sevree  │   OU    │ statut: chaleurs │          │
│  │ type: truie/    │         │ type: cochette   │          │
│  │       cochette  │         │ + 1ère fois      │          │
│  └─────────────────┘         └──────────────────┘          │
│         ↓                             ↓                      │
│         └──────────── CRÉER ──────────┘                      │
│                   NOUVEAU CYCLE                              │
│                        ↓                                     │
│              CycleReproduction::create([                     │
│                 numero_cycle: auto,                          │
│                 date_debut: now(),                           │
│                 statut_cycle: 'en_cours'                     │
│              ])                                              │
└──────────────────────────────────────────────────────────────┘
```

## Tests à ajouter (recommandé)

Pour garantir le bon fonctionnement de cet observer, voici des tests recommandés :

```php
it('creates new cycle when sow is weaned', function () {
    $truie = Animal::factory()->create([
        'type_animal' => 'truie',
        'statut_actuel' => 'en_lactation',
    ]);

    // Créer un cycle existant
    CycleReproduction::factory()->create([
        'animal_id' => $truie->id,
        'numero_cycle' => 5,
        'statut_cycle' => 'en_cours',
    ]);

    // Changer le statut vers sevree
    $truie->update(['statut_actuel' => 'sevree']);

    // Vérifier qu'un nouveau cycle #6 a été créé
    expect(CycleReproduction::where('animal_id', $truie->id)->count())->toBe(2);

    $nouveauCycle = CycleReproduction::where('animal_id', $truie->id)
        ->orderBy('numero_cycle', 'desc')
        ->first();

    expect($nouveauCycle->numero_cycle)->toBe(6)
        ->and($nouveauCycle->statut_cycle)->toBe('en_cours');
});

it('creates first cycle for gilt in first heat', function () {
    $cochette = Animal::factory()->create([
        'type_animal' => 'cochette',
        'statut_actuel' => 'active',
    ]);

    // Aucun cycle existant
    expect(CycleReproduction::where('animal_id', $cochette->id)->count())->toBe(0);

    // Premières chaleurs
    $cochette->update(['statut_actuel' => 'en_chaleurs']);

    // Vérifier qu'un cycle #1 a été créé
    $cycle = CycleReproduction::where('animal_id', $cochette->id)->first();

    expect($cycle)->not->toBeNull()
        ->and($cycle->numero_cycle)->toBe(1);
});
```

## Résumé

L'`AnimalObserver` est un composant **ESSENTIEL** de votre application qui :

1. **Surveille** automatiquement les changements de statut des animaux
2. **Crée** automatiquement de nouveaux cycles de reproduction après sevrage
3. **Incrémente** automatiquement le numéro de cycle
4. **Permet** le cycle perpétuel de reproduction des truies sans intervention manuelle

C'est un excellent exemple de **DDD (Domain-Driven Design)** et de **logique métier encapsulée** ! 🎯
