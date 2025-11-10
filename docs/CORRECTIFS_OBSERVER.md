# Correctifs apportés à l'AnimalObserver

## Problèmes identifiés

### ❌ Problème 1 : Cochette en chaleurs ne créait pas de cycle

**Symptôme** : Lorsqu'on changeait le statut d'une cochette vers `en_chaleurs`, aucun cycle de reproduction n'était créé automatiquement.

**Cause** : La méthode `estPremiereChaleur()` utilisait `exists()` d'une manière qui pouvait ne pas fonctionner correctement dans tous les contextes.

**Solution** : Remplacement par une vérification explicite avec `count()` :

```php
// AVANT (ne fonctionnait pas toujours)
protected function estPremiereChaleur(Animal $animal): bool
{
    return $animal->type_animal === 'cochette'
        && !CycleReproduction::where('animal_id', $animal->id)->exists();
}

// APRÈS (fonctionne correctement)
protected function doitCreerCyclePourChaleurs(Animal $animal): bool
{
    if ($animal->type_animal === 'cochette') {
        $nbCycles = CycleReproduction::where('animal_id', $animal->id)->count();
        if ($nbCycles === 0) {
            return true; // Première chaleur
        }
    }
    // ...
}
```

### ❌ Problème 2 : Pas de protection contre les cycles multiples

**Symptôme** : Il était possible de créer plusieurs cycles `en_cours` pour un même animal, créant des incohérences dans les données.

**Cause** : Aucune vérification n'était faite pour empêcher la création d'un nouveau cycle si le dernier était encore actif.

**Solution** : Ajout d'une vérification systématique avant toute création de cycle :

```php
// RÈGLE IMPORTANTE : Ne jamais créer de cycle si le dernier est encore en_cours
if ($this->aCycleEnCours($animal)) {
    Log::info('AnimalObserver: Cycle en cours détecté, pas de création de nouveau cycle');
    return;
}

protected function aCycleEnCours(Animal $animal): bool
{
    return CycleReproduction::where('animal_id', $animal->id)
        ->where('statut_cycle', 'en_cours')
        ->exists();
}
```

## Améliorations apportées

### ✅ 1. Logging détaillé

Ajout de logs pour faciliter le débogage :

```php
Log::info("AnimalObserver: Changement de statut pour {$animal->numero_identification}", [
    'ancien_statut' => $ancienStatut,
    'nouveau_statut' => $nouveauStatut,
    'type_animal' => $animal->type_animal,
]);
```

**Utilité** : Permet de voir dans les logs Laravel (`storage/logs/laravel.log`) quand l'observer se déclenche et ce qu'il fait.

### ✅ 2. Gestion des cycles échoués

Ajout de la logique pour gérer le retour en chaleurs après un cycle échoué :

```php
// Cas 2 : Truie/Cochette avec dernier cycle terminé en échec
$dernierCycle = CycleReproduction::where('animal_id', $animal->id)
    ->orderBy('numero_cycle', 'desc')
    ->first();

if ($dernierCycle && $dernierCycle->statut_cycle === 'termine_echec') {
    Log::info("AnimalObserver: Retour en chaleurs après échec pour {$animal->numero_identification}");
    return true;
}
```

**Scénario couvert** :
1. Truie saillie → Cycle #5 en_cours
2. Diagnostic négatif → Cycle #5 passe à `termine_echec`
3. Truie revient en chaleurs → Création automatique du Cycle #6

### ✅ 3. Méthode dédiée pour les chaleurs

Remplacement de la méthode `estPremiereChaleur()` par une méthode plus complète `doitCreerCyclePourChaleurs()` qui gère :
- Les premières chaleurs des cochettes
- Le retour en chaleurs après échec pour truies/cochettes

## Nouveaux tests

8 tests ont été créés pour garantir le bon fonctionnement :

| Test | Description |
|------|-------------|
| ✅ creates new cycle when sow is weaned | Cycle créé après sevrage |
| ✅ creates first cycle for gilt in first heat | Cycle créé pour première chaleur cochette |
| ✅ does not create cycle if one is already in progress | **PROTECTION** : Pas de cycle si un existe déjà |
| ✅ creates new cycle when returning to heat after failed cycle | Cycle créé après échec |
| ✅ does not create cycle for gilt already having cycles | Pas de cycle si cochette a déjà des cycles |
| ✅ does not create cycle when status changes to other values | Pas de cycle pour autres statuts |
| ✅ increments cycle number correctly | Numérotation correcte |
| ✅ creates cycle with correct initial data | Données initiales correctes |

## Comportement actuel (corrigé)

### Déclencheurs de création de cycle

L'observer crée automatiquement un nouveau cycle dans **3 situations** :

#### 1️⃣ Sevrage (truie/cochette)
```
Statut: en_lactation → sevree
Condition: Aucun cycle en_cours
Résultat: Création du cycle suivant (N+1)
```

#### 2️⃣ Première chaleur (cochette)
```
Type: cochette
Statut: active → en_chaleurs
Condition: Aucun cycle existant ET aucun cycle en_cours
Résultat: Création du cycle #1
```

#### 3️⃣ Retour en chaleurs après échec
```
Statut: gestante_attente → en_chaleurs
Condition: Dernier cycle = termine_echec ET aucun cycle en_cours
Résultat: Création d'un nouveau cycle
```

### Protection contre les doublons

**RÈGLE ABSOLUE** : Si un cycle est `en_cours`, **AUCUN** nouveau cycle ne peut être créé, peu importe le changement de statut.

Cette règle garantit qu'un animal ne peut jamais avoir deux cycles actifs simultanément.

## Vérification dans les logs

Pour vérifier que l'observer fonctionne correctement, consultez `storage/logs/laravel.log` :

```
[2025-11-09 10:30:15] local.INFO: AnimalObserver: Changement de statut pour AN-1234
{"ancien_statut":"en_lactation","nouveau_statut":"sevree","type_animal":"truie"}

[2025-11-09 10:30:15] local.INFO: AnimalObserver: Création d'un nouveau cycle pour AN-1234
{"cycle_id":42}

[2025-11-09 10:30:15] local.INFO: AnimalObserver: Cycle #6 créé pour AN-1234
```

## Requêtes SQL pour vérifier la cohérence

### Vérifier qu'aucun animal n'a 2 cycles en_cours

```sql
SELECT
    animal_id,
    COUNT(*) as nb_cycles_en_cours
FROM cycles_reproduction
WHERE statut_cycle = 'en_cours'
GROUP BY animal_id
HAVING COUNT(*) > 1;
-- Cette requête doit retourner 0 lignes !
```

### Vérifier les cochettes avec cycles

```sql
SELECT
    a.numero_identification,
    a.type_animal,
    a.statut_actuel,
    COUNT(cr.id) as nb_cycles
FROM animaux a
LEFT JOIN cycles_reproduction cr ON a.id = cr.animal_id
WHERE a.type_animal = 'cochette'
GROUP BY a.id
ORDER BY nb_cycles DESC;
```

## Résumé des changements

| Aspect | Avant | Après |
|--------|-------|-------|
| Détection première chaleur | ❌ Ne fonctionnait pas toujours | ✅ Fonctionne avec `count()` |
| Protection double cycle | ❌ Aucune protection | ✅ Vérification systématique |
| Gestion échecs | ❌ Pas gérée | ✅ Nouveau cycle après échec |
| Logging | ❌ Aucun log | ✅ Logs détaillés |
| Tests | ❌ 0 test | ✅ 8 tests complets |

## Impact sur l'utilisation

### Pour l'éleveur

**Avant** :
- Il fallait créer manuellement un cycle après le sevrage
- Les cochettes en première chaleur nécessitaient une création manuelle
- Risque d'oublier de créer un cycle

**Après** :
- ✅ Cycle créé automatiquement après sevrage
- ✅ Cycle créé automatiquement pour les cochettes
- ✅ Cycle créé automatiquement après échec
- ✅ Protection contre les doublons
- ✅ Logs pour tracer toutes les opérations

### Pour le développeur

- Meilleure traçabilité avec les logs
- Tests automatisés garantissent le bon fonctionnement
- Code plus robuste et maintenable
- Documentation complète du comportement

## Compatibilité

Ces changements sont **rétrocompatibles** :
- Aucun changement dans la base de données
- Aucun changement dans l'interface
- Comportement amélioré sans breaking changes

Les cycles existants ne sont pas affectés.
