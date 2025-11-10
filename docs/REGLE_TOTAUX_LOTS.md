# Règle fondamentale : Cohérence des totaux Lots-Portées

## Principe de base

**Le nombre de porcelets d'un lot DOIT TOUJOURS correspondre EXACTEMENT à la somme des porcelets de toutes les portées qui constituent ce lot.**

Cette règle est CRITIQUE pour la traçabilité et la cohérence des données.

## Formules mathématiques

### Pour un lot donné :

```
lot.nb_animaux_depart = Σ(portee.nb_sevres) pour toutes les portées du lot
lot.poids_total_depart_kg = Σ(portee.poids_total_sevrage_kg) pour toutes les portées du lot
lot.poids_moyen_depart_kg = lot.poids_total_depart_kg / lot.nb_animaux_depart
```

### Exemple concret :

```
Portée 1 (Truie #123) : 11 porcelets, 77.0 kg
Portée 2 (Truie #456) : 10 porcelets, 70.0 kg
Portée 3 (Truie #789) : 12 porcelets, 84.0 kg
----------------------------------------
LOT-PS-S44-24       : 33 porcelets, 231.0 kg (= 7.0 kg/porcelet)
```

## Vérifications dans le code

### 1. Dans l'action de sevrage (`SevrerPorteesAction.php`)

```php
// Les totaux sont calculés en additionnant les données réelles
$totalPorcelets = 0;
$totalPoids = 0;

foreach ($data['donnees_portees'] as $donneePortee) {
    $totalPorcelets += $donneePortee['nb_sevres'];
    $totalPoids += $donneePortee['poids_total_kg'];
}

// Ces totaux sont DIRECTEMENT utilisés pour le lot
updateLot($lot, $totalPorcelets, $totalPoids, $data);
```

### 2. Dans la table pivot `lot_portee`

La table pivot enregistre également ces informations pour chaque portée :

```sql
SELECT
    l.numero_lot,
    l.nb_animaux_depart AS "Total lot",
    SUM(lp.nb_porcelets_transferes) AS "Somme pivot"
FROM lots l
JOIN lot_portee lp ON l.id = lp.lot_id
GROUP BY l.id
HAVING l.nb_animaux_depart != SUM(lp.nb_porcelets_transferes);
-- Cette requête doit TOUJOURS retourner 0 lignes !
```

## Interface utilisateur

L'interface Filament affiche des **totaux récapitulatifs en temps réel** pendant la saisie :

- **Total porcelets** : Somme automatique des nb_sevres saisis
- **Poids total** : Somme automatique des poids_total_kg saisis
- **Poids moyen global** : Calcul automatique du poids moyen

Ces totaux permettent à l'utilisateur de vérifier ses saisies AVANT validation.

## Tests automatisés

Le test `it('ensures lot totals equal sum of all litters')` vérifie cette règle :

```php
// Créer 3 portées : 10, 11, 12 porcelets
$totalAttendu = 33 porcelets

// Sevrer vers un lot
$lot = sevrageGroupé($portees);

// VÉRIFICATION
expect($lot->nb_animaux_depart)->toBe(33);
expect($lot->nb_animaux_actuel)->toBe(33);
```

## Cas particuliers

### Ajout à un lot existant

Si on ajoute des portées à un lot existant :

```
Lot existant : 50 porcelets, 350 kg
+ Portée nouvelle : 11 porcelets, 77 kg
----------------------------------------
Lot après ajout : 61 porcelets, 427 kg
```

Le code gère correctement ce cas :

```php
if ($lot->nb_animaux_depart === 0) {
    // Premier remplissage
    $lot->nb_animaux_depart = $totalPorcelets;
} else {
    // Ajout à un lot existant
    $lot->nb_animaux_actuel += $totalPorcelets;
    $lot->poids_total_actuel_kg += $totalPoids;
}
```

### Mortalité en cours d'élevage

**IMPORTANT** : Cette règle s'applique au moment du sevrage. Ensuite, pendant la période de post-sevrage ou d'engraissement, des porcelets peuvent mourir, donc :

```
nb_animaux_actuel peut diminuer avec le temps
nb_animaux_depart reste constant (référence historique)
```

Mais à tout moment :
```
nb_animaux_depart = Σ(nb_sevres de toutes les portées du lot)
```

## Résumé

✅ **À FAIRE** :
- Calculer les totaux du lot en additionnant les données réelles des portées
- Afficher les totaux à l'utilisateur avant validation
- Vérifier la cohérence dans les tests

❌ **À NE JAMAIS FAIRE** :
- Saisir manuellement un total qui ne correspond pas à la somme
- Modifier les effectifs du lot sans mettre à jour les portées correspondantes
- Ignorer les données de la table pivot `lot_portee`

## Traçabilité complète

Grâce à cette règle et à la table pivot, on peut TOUJOURS répondre aux questions :

1. **Quel lot contient les porcelets de la Truie #123 ?**
   → Requête sur `portees` : `WHERE animal_id = 123 AND lot_destination_id IS NOT NULL`

2. **Combien de porcelets du Lot-PS-S44-24 viennent de la Truie #123 ?**
   → Requête sur `lot_portee` avec jointure

3. **Le total du lot correspond-il bien à ses portées ?**
   → Vérification automatique dans les tests

Cette cohérence est ESSENTIELLE pour la gestion sanitaire, la traçabilité alimentaire, et les analyses de performance de l'élevage.
