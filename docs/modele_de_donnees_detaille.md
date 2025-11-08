# Modèle de Données Détaillé : Application de Gestion d'Élevage Porcin

## Architecture Générale du Modèle de Données

Votre base de données va s'organiser autour de trois grands domaines qui correspondent aux trois axes de gestion de l'application. Le premier domaine concerne les entités de base (les animaux, les salles, les plans d'alimentation). Le deuxième domaine gère le cycle de reproduction (la partie individuelle). Le troisième domaine gère la production collective (les lots). Et au centre de tout cela, il y a les événements et les tâches qui permettent de suivre tout ce qui se passe dans l'élevage.

---

## 1. Les Tables de Référence (Configuration de Base)

Ces tables contiennent les données qui ne changent pas souvent et qui servent de référence pour le reste du système.

### Table : `races`

Cette table stocke les différentes races de porcs utilisées dans l'élevage. C'est important car chaque race a des caractéristiques de croissance et de reproduction différentes.

#### Champs détaillés

**`id`** (bigint, clé primaire, auto-incrémenté)
- Description : L'identifiant unique de la race
- Exemple : 1, 2, 3

**`nom`** (string, 100 caractères, obligatoire, unique)
- Description : Le nom de la race
- Exemple : "Large White", "Landrace", "Piétrain", "Duroc"

**`description`** (text, nullable)
- Description : Une description des caractéristiques de la race
- Exemple : "Race maternelle reconnue pour sa prolificité et ses qualités maternelles. Excellente production laitière."

**`type`** (enum: 'maternelle', 'paternelle', 'mixte', obligatoire)
- Description : Le type génétique de la race. Les races maternelles sont choisies pour leurs qualités de reproduction, les races paternelles pour la qualité de la viande
- Exemple : "maternelle"

**`gmq_moyen`** (decimal 5,2, nullable)
- Description : Le Gain Moyen Quotidien attendu pour cette race en grammes par jour
- Exemple : 750.00 (ce qui signifie 750 grammes de gain par jour)

**`poids_adulte_moyen`** (decimal 6,2, nullable)
- Description : Le poids moyen à l'âge adulte en kilogrammes
- Exemple : 280.00

**`created_at`** et **`updated_at`** (timestamp)
- Description : Dates de création et de modification automatiques gérées par Laravel

#### Exemple de données
```
id: 1
nom: "Large White"
type: "maternelle"
gmq_moyen: 750.00
poids_adulte_moyen: 280.00
```

---

### Table : `type_salles`

Cette table définit les différents types de salles dans l'élevage. Chaque type de salle a des caractéristiques spécifiques et accueille des animaux à des stades différents.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`nom`** (string, 100 caractères, unique)
- Description : Le nom du type de salle
- Exemple : "Maternité", "Post-Sevrage", "Engraissement", "Gestantes", "Verraterie"

**`description`** (text, nullable)
- Description : Description et spécificités du type de salle
- Exemple : "Salle équipée de cases individuelles avec nids chauffants pour les porcelets. Température contrôlée entre 20-22°C pour la truie et 28-32°C pour le nid."

**`capacite_type`** (enum: 'individuelle', 'collective')
- Description : Indique si la salle contient des places individuelles ou des parcs collectifs
- Exemple : "individuelle" pour la maternité, "collective" pour l'engraissement

**`temperature_optimale`** (decimal 4,1, nullable)
- Description : Température recommandée en degrés Celsius
- Exemple : 21.5

**`created_at`** et **`updated_at`**

---

### Table : `salles`

Cette table représente les salles physiques réelles de l'élevage. Chaque salle est une instance concrète d'un type de salle.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`type_salle_id`** (bigint, clé étrangère vers type_salles, obligatoire)
- Description : Le type de cette salle

**`nom`** (string, 100 caractères, unique)
- Description : Nom ou numéro de la salle
- Exemple : "Maternité A", "MAT-01", "Engraissement Bâtiment 2"

**`capacite`** (integer, obligatoire)
- Description : Nombre de places disponibles
- Exemple : 12 (pour 12 cases de maternité) ou 200 (pour 200 porcs en engraissement)

**`statut`** (enum: 'disponible', 'occupée', 'vide_sanitaire', 'maintenance', défaut: 'disponible')
- Description : L'état actuel de la salle. Le vide sanitaire est une période obligatoire de nettoyage entre deux bandes d'animaux

**`date_debut_vide_sanitaire`** (date, nullable)
- Description : Si la salle est en vide sanitaire, quand a-t-il commencé
- Exemple : "2025-01-15"

**`duree_vide_sanitaire_jours`** (integer, défaut: 7)
- Description : Durée standard du vide sanitaire pour cette salle en jours
- Exemple : 7

**`notes`** (text, nullable)
- Description : Notes diverses sur la salle
- Exemple : "Problème de ventilation réparé le 10/01/2025"

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 1
type_salle_id: 1 (Maternité)
nom: "MAT-A01"
capacite: 12
statut: "occupée"
```

---

### Table : `plan_alimentations`

Cette table définit les différents plans d'alimentation utilisés dans l'élevage. Chaque plan correspond à un stade physiologique ou de croissance différent.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`nom`** (string, 100 caractères, unique)
- Description : Nom du plan
- Exemple : "Flushing", "Gestation Phase 1", "Lactation", "Post-Sevrage 1er Âge", "Engraissement Finition"

**`type_animal`** (enum: 'reproducteur', 'production', obligatoire)
- Description : Indique si ce plan est pour des reproducteurs (gestion individuelle) ou pour des animaux de production (gestion collective)

**`description`** (text, nullable)
- Description : Description détaillée du plan et de ses objectifs
- Exemple : "Alimentation hautement énergétique pour stimuler l'ovulation avant la saillie. Augmentation de 1kg par jour pendant 7-10 jours."

**`energie_mcal_jour`** (decimal 6,2, nullable)
- Description : Apport énergétique quotidien en mégacalories
- Exemple : 6.50

**`proteine_pourcent`** (decimal 4,1, nullable)
- Description : Pourcentage de protéines dans l'aliment
- Exemple : 15.5

**`ration_kg_jour`** (decimal 5,2, nullable)
- Description : Quantité d'aliment en kilogrammes par jour et par animal
- Exemple : 2.80
- Note : ce champ peut être nullable car certains plans sont "à volonté"

**`a_volonte`** (boolean, défaut: false)
- Description : Indique si l'alimentation est distribuée à volonté
- Exemple : true pour la lactation

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 5
nom: "Lactation"
type_animal: "reproducteur"
energie_mcal_jour: 18.00
a_volonte: true
```

---

### Table : `produits_sanitaires`

Cette table stocke tous les produits vétérinaires et sanitaires utilisés (vaccins, antibiotiques, antiparasitaires, etc.).

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`nom`** (string, 150 caractères, obligatoire)
- Description : Nom commercial du produit
- Exemple : "Porcilis® Ery+Parvo+Lepto", "Ivomec®"

**`type`** (enum: 'vaccin', 'antibiotique', 'antiparasitaire', 'autre', obligatoire)
- Description : Catégorie du produit

**`laboratoire`** (string, 100 caractères, nullable)
- Description : Nom du fabricant
- Exemple : "MSD Animal Health"

**`principe_actif`** (string, 200 caractères, nullable)
- Description : Molécule active
- Exemple : "Ivermectine"

**`numero_amm`** (string, 50 caractères, nullable)
- Description : Numéro d'Autorisation de Mise sur le Marché. Important pour la traçabilité réglementaire

**`delai_attente_jours`** (integer, nullable)
- Description : Délai légal avant abattage après utilisation du produit en jours
- Exemple : 28

**`voie_administration`** (enum: 'injectable', 'orale', 'topique', nullable)
- Description : Mode d'administration

**`dosage_ml_kg`** (decimal 6,3, nullable)
- Description : Dosage en millilitres par kilogramme de poids vif
- Exemple : 1.000

**`stock_actuel`** (integer, défaut: 0)
- Description : Quantité en stock (nombre de doses ou de flacons selon l'unité)

**`stock_alerte`** (integer, nullable)
- Description : Seuil d'alerte pour réapprovisionnement
- Exemple : 10

**`created_at`** et **`updated_at`**

---

## 2. Les Tables des Animaux (Gestion Individuelle)

Ces tables gèrent les animaux suivis individuellement, c'est-à-dire les reproducteurs.

### Table : `animaux`

C'est la table centrale pour tous les animaux suivis individuellement. Elle contient à la fois les truies, les cochettes et les verrats.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`numero_identification`** (string, 50 caractères, unique, obligatoire)
- Description : Le numéro officiel d'identification de l'animal, souvent le numéro de boucle auriculaire
- Exemple : "FR12345678901", "T-2024-001"

**`type_animal`** (enum: 'truie', 'cochette', 'verrat', obligatoire)
- Description : Le type d'animal. Une cochette devient une truie après sa première mise-bas

**`race_id`** (bigint, clé étrangère vers races, obligatoire)
- Description : La race de l'animal

**`sexe`** (enum: 'F', 'M', obligatoire)
- Description : Le sexe biologique de l'animal

**`date_naissance`** (date, obligatoire)
- Description : Date de naissance de l'animal
- Exemple : "2023-06-15"

**`date_entree`** (date, obligatoire)
- Description : Date d'entrée dans l'élevage. Peut être identique à la date de naissance si l'animal est né sur place
- Exemple : "2023-09-20"

**`origine`** (enum: 'naissance_elevage', 'achat_externe', obligatoire)
- Description : Comment l'animal est arrivé dans l'élevage

**`numero_mere`** (string, 50 caractères, nullable)
- Description : Numéro d'identification de la mère si connue. Permet la généalogie

**`numero_pere`** (string, 50 caractères, nullable)
- Description : Numéro d'identification du père si connu

**`statut_actuel`** (enum: 'sevree', 'en_chaleurs', 'gestante_attente', 'gestante_confirmee', 'en_lactation', 'reforme', 'active', 'retraite', obligatoire)
- Description : Le statut physiologique actuel de l'animal. Pour les verrats, on utilise généralement 'active' ou 'retraite'. Pour les truies/cochettes, le statut change selon le cycle

**`salle_id`** (bigint, clé étrangère vers salles, nullable)
- Description : Dans quelle salle se trouve actuellement l'animal

**`place_numero`** (string, 20 caractères, nullable)
- Description : Numéro de la case ou de l'emplacement précis dans la salle
- Exemple : "A-12", "Box 3"

**`poids_actuel_kg`** (decimal 6,2, nullable)
- Description : Dernier poids enregistré en kilogrammes
- Exemple : 185.50

**`date_derniere_pesee`** (date, nullable)
- Description : Date de la dernière pesée
- Exemple : "2025-01-10"

**`plan_alimentation_id`** (bigint, clé étrangère vers plan_alimentations, nullable)
- Description : Le plan d'alimentation actuellement appliqué à cet animal

**`bande`** (string, 50 caractères, nullable)
- Description : Nom de la bande à laquelle appartient l'animal
- Exemple : "2025-S10", "Bande Mars 2025"

**`date_reforme`** (date, nullable)
- Description : Si l'animal est réformé (sorti de l'élevage pour vente ou abattage), la date de réforme

**`motif_reforme`** (text, nullable)
- Description : Raison de la réforme
- Exemple : "Baisse de prolificité", "Problèmes locomoteurs", "Âge"

**`notes`** (text, nullable)
- Description : Notes libres sur l'animal

**`created_at`** et **`updated_at`**

**`deleted_at`** (timestamp, nullable)
- Description : Pour le soft delete Laravel. Permet de "supprimer" un animal sans perdre son historique

#### Exemple de données
```
id: 42
numero_identification: "FR2024-T-042"
type_animal: "truie"
race_id: 1
date_naissance: "2022-03-15"
statut_actuel: "gestante_confirmee"
bande: "2025-S08"
poids_actuel_kg: 215.00
```

---

### Table : `cycles_reproduction`

C'est une table cruciale qui enregistre chaque tentative de reproduction d'une truie ou cochette. Chaque ligne représente un cycle complet qui commence au sevrage (ou aux premières chaleurs pour une cochette) et se termine soit par une mise-bas réussie, soit par un échec.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`animal_id`** (bigint, clé étrangère vers animaux, obligatoire)
- Description : La truie ou cochette concernée

**`numero_cycle`** (integer, obligatoire)
- Description : Le numéro de cycle pour cette truie. Le premier cycle d'une cochette est le numéro 1
- Exemple : 1, 2, 3... jusqu'à 8 ou 10 pour une truie productive

**`date_debut`** (date, obligatoire)
- Description : Date de début du cycle, généralement la date du sevrage précédent ou la date des premières chaleurs pour une cochette
- Exemple : "2025-01-05"

**`date_chaleurs`** (datetime, nullable)
- Description : Date et heure précises de détection des chaleurs
- Exemple : "2025-01-12 09:30:00"

**`date_premiere_saillie`** (datetime, nullable)
- Description : Date et heure de la première insémination ou saillie. C'est le J0 de la gestation
- Exemple : "2025-01-12 15:00:00"

**`type_saillie`** (enum: 'IA', 'MN', nullable)
- Description : Type de reproduction. IA = Insémination Artificielle, MN = Monte Naturelle

**`date_diagnostic`** (date, nullable)
- Description : Date du diagnostic de gestation (échographie)
- Exemple : "2025-02-05"

**`resultat_diagnostic`** (enum: 'positif', 'negatif', 'en_attente', défaut: 'en_attente')
- Description : Résultat du diagnostic de gestation

**`date_mise_bas_prevue`** (date, nullable)
- Description : Date calculée de mise-bas prévue (J0 + 115 jours généralement)
- Exemple : "2025-05-07"

**`date_mise_bas_reelle`** (date, nullable)
- Description : Date réelle de la mise-bas si elle a eu lieu

**`statut_cycle`** (enum: 'en_cours', 'termine_succes', 'termine_echec', 'avorte', obligatoire, défaut: 'en_cours')
- Description : Le statut du cycle

**`motif_echec`** (text, nullable)
- Description : Si le cycle a échoué, pourquoi
- Exemple : "Retour en chaleurs J21", "Avortement J45", "Diagnostic négatif"

**`notes`** (text, nullable)

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 156
animal_id: 42
numero_cycle: 3
date_debut: "2025-01-05"
date_premiere_saillie: "2025-01-12 15:00:00"
resultat_diagnostic: "positif"
date_mise_bas_prevue: "2025-05-07"
statut_cycle: "en_cours"
```

#### Relations importantes
Un animal peut avoir plusieurs cycles de reproduction (relation one-to-many). Un cycle de reproduction peut avoir plusieurs saillies (voir table suivante).

---

### Table : `saillies`

Cette table enregistre chaque acte d'insémination ou de saillie. Il peut y avoir plusieurs saillies pour un même cycle de reproduction (inséminations répétées pendant les mêmes chaleurs).

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`cycle_reproduction_id`** (bigint, clé étrangère vers cycles_reproduction, obligatoire)
- Description : Le cycle auquel appartient cette saillie

**`date_heure`** (datetime, obligatoire)
- Description : Date et heure précises de la saillie
- Exemple : "2025-01-12 15:00:00"

**`type`** (enum: 'IA', 'MN', obligatoire)
- Description : Type d'insémination

**`verrat_id`** (bigint, clé étrangère vers animaux, nullable)
- Description : Si c'est une monte naturelle, quel verrat. Si c'est une IA, ce champ peut être null ou contenir le verrat dont provient la semence

**`semence_lot_numero`** (string, 100 caractères, nullable)
- Description : Pour une IA, le numéro du lot de semence utilisé. Important pour la traçabilité
- Exemple : "LOT-V15-2025-003"

**`intervenant`** (string, 100 caractères, nullable)
- Description : Nom de la personne qui a effectué l'acte
- Exemple : "Jean Dupont"

**`notes`** (text, nullable)
- Description : Observations
- Exemple : "Bonne réceptivité de la truie", "Semence diluée 1:3"

**`created_at`** et **`updated_at`**

#### Relations
Chaque saillie appartient à un cycle de reproduction (many-to-one).

---

### Table : `portees`

Cette table enregistre chaque mise-bas et le suivi des porcelets jusqu'au sevrage. C'est le lien entre la gestion individuelle (la mère) et le début de la vie des porcelets.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`cycle_reproduction_id`** (bigint, clé étrangère vers cycles_reproduction, obligatoire)
- Description : Le cycle de reproduction qui a donné naissance à cette portée

**`animal_id`** (bigint, clé étrangère vers animaux, obligatoire)
- Description : La truie qui a mis bas. (Redondant avec cycle_reproduction_id mais utile pour les requêtes rapides)

**`date_mise_bas`** (datetime, obligatoire)
- Description : Date et heure de la mise-bas
- Exemple : "2025-05-07 02:30:00"

**`nb_nes_vifs`** (integer, obligatoire)
- Description : Nombre de porcelets nés vivants
- Exemple : 13

**`nb_mort_nes`** (integer, défaut: 0)
- Description : Nombre de mort-nés
- Exemple : 1

**`nb_momifies`** (integer, défaut: 0)
- Description : Nombre de momifiés (fœtus morts pendant la gestation)
- Exemple : 0

**`nb_total`** (integer, généré, stocké)
- Description : Total des porcelets (vifs + mort-nés + momifiés). Calculé automatiquement
- Exemple : 14

**`poids_moyen_naissance_g`** (integer, nullable)
- Description : Poids moyen des porcelets à la naissance en grammes
- Exemple : 1350

**`date_sevrage`** (date, nullable)
- Description : Date du sevrage de la portée
- Exemple : "2025-05-28"

**`nb_sevres`** (integer, nullable)
- Description : Nombre de porcelets effectivement sevrés. Peut être inférieur au nombre de nés vifs en cas de mortalité
- Exemple : 12

**`poids_total_sevrage_kg`** (decimal 7,2, nullable)
- Description : Poids total de tous les porcelets au sevrage en kilogrammes
- Exemple : 84.50

**`poids_moyen_sevrage_kg`** (decimal 5,2, nullable, calculé)
- Description : Poids moyen par porcelet au sevrage. Calculé : poids_total / nb_sevres
- Exemple : 7.04

**`lot_destination_id`** (bigint, clé étrangère vers lots, nullable)
- Description : Le lot dans lequel les porcelets sevrés ont été placés. C'est LE champ qui fait le lien entre gestion individuelle et gestion collective

**`notes`** (text, nullable)

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 89
cycle_reproduction_id: 156
animal_id: 42
date_mise_bas: "2025-05-07 02:30:00"
nb_nes_vifs: 13
nb_sevres: 12
date_sevrage: "2025-05-28"
lot_destination_id: 45
```

#### Relations clés
Une portée appartient à un cycle de reproduction (many-to-one). Une portée appartient à un animal truie (many-to-one). Une portée peut être assignée à un lot (many-to-one), c'est le pivot vers la gestion collective.

---

## 3. Les Tables de Production Collective (Gestion par Lots)

À partir du sevrage, on passe à une logique complètement différente où on ne suit plus les animaux individuellement mais par groupes homogènes.

### Table : `lots`

Cette table représente un groupe de porcelets ou porcs gérés ensemble. C'est l'entité centrale de la gestion collective.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`numero_lot`** (string, 100 caractères, unique, obligatoire)
- Description : Identifiant unique du lot
- Exemple : "LOT-PS-S08-2025", "LOT-ENG-2025-003"

**`type_lot`** (enum: 'post_sevrage', 'engraissement', obligatoire)
- Description : Le stade de production du lot

**`date_creation`** (date, obligatoire)
- Description : Date de création du lot, généralement la date du sevrage
- Exemple : "2025-05-28"

**`nb_animaux_depart`** (integer, obligatoire)
- Description : Nombre d'animaux au départ du lot
- Exemple : 180

**`nb_animaux_actuel`** (integer, obligatoire)
- Description : Nombre d'animaux actuellement vivants dans le lot. Ce nombre diminue en cas de mortalité
- Exemple : 175

**`poids_total_depart_kg`** (decimal 8,2, nullable)
- Description : Poids total du lot à la création
- Exemple : 1260.00

**`poids_moyen_depart_kg`** (decimal 6,2, nullable, calculé)
- Description : Poids moyen par animal au départ
- Exemple : 7.00

**`poids_total_actuel_kg`** (decimal 8,2, nullable)
- Description : Dernier poids total mesuré
- Exemple : 4725.00

**`poids_moyen_actuel_kg`** (decimal 6,2, nullable, calculé)
- Description : Poids moyen actuel par animal
- Exemple : 27.00

**`date_derniere_pesee`** (date, nullable)
- Description : Date de la dernière pesée
- Exemple : "2025-06-15"

**`salle_id`** (bigint, clé étrangère vers salles, nullable)
- Description : Salle où se trouve actuellement le lot

**`statut_lot`** (enum: 'actif', 'transfere', 'vendu', 'cloture', obligatoire, défaut: 'actif')
- Description : Statut du lot

**`plan_alimentation_id`** (bigint, clé étrangère vers plan_alimentations, nullable)
- Description : Plan d'alimentation actuellement appliqué au lot

**`date_sortie`** (date, nullable)
- Description : Date de sortie/vente du lot
- Exemple : "2025-08-20"

**`nb_animaux_sortie`** (integer, nullable)
- Description : Nombre d'animaux vendus
- Exemple : 172

**`poids_total_sortie_kg`** (decimal 8,2, nullable)
- Description : Poids total à la vente
- Exemple : 19780.00

**`poids_moyen_sortie_kg`** (decimal 6,2, nullable)
- Description : Poids moyen à la vente
- Exemple : 115.00

**`prix_vente_total`** (decimal 10,2, nullable)
- Description : Montant total de la vente en euros
- Exemple : 23736.00

**`destination_sortie`** (string, 200 caractères, nullable)
- Description : Où sont allés les animaux
- Exemple : "Abattoir Cooperl - Châteaubriant"

**`notes`** (text, nullable)

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 45
numero_lot: "LOT-PS-S08-2025"
type_lot: "post_sevrage"
date_creation: "2025-05-28"
nb_animaux_depart: 180
nb_animaux_actuel: 175
statut_lot: "actif"
```

#### Relations
Un lot peut contenir des porcelets provenant de plusieurs portées (many-to-many via la table portees).

---

## 4. Les Tables d'Événements et de Suivi

Ces tables enregistrent tout ce qui se passe dans l'élevage au quotidien. C'est l'historique complet de l'exploitation.

### Table : `evenements_sanitaires`

Cette table enregistre tous les actes vétérinaires et sanitaires, que ce soit sur des animaux individuels ou sur des lots.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`type_cible`** (enum: 'animal', 'lot', obligatoire)
- Description : Est-ce qu'on traite un animal individuel ou un lot entier

**`animal_id`** (bigint, clé étrangère vers animaux, nullable)
- Description : Si type_cible = 'animal', l'animal concerné

**`lot_id`** (bigint, clé étrangère vers lots, nullable)
- Description : Si type_cible = 'lot', le lot concerné

**`date_evenement`** (datetime, obligatoire)
- Description : Date et heure de l'intervention
- Exemple : "2025-06-01 10:15:00"

**`type_evenement`** (enum: 'vaccination', 'traitement', 'castration', 'caudectomie', 'autre', obligatoire)
- Description : Type d'intervention

**`produit_sanitaire_id`** (bigint, clé étrangère vers produits_sanitaires, nullable)
- Description : Produit utilisé si applicable

**`dose_administree`** (decimal 8,3, nullable)
- Description : Dose donnée (l'unité dépend du produit)
- Exemple : 2.000

**`nb_animaux_traites`** (integer, nullable)
- Description : Si c'est un lot, combien d'animaux ont été traités
- Exemple : 175

**`intervenant`** (string, 100 caractères, nullable)
- Description : Qui a réalisé l'acte
- Exemple : "Dr. Martin Vétérinaire"

**`motif`** (text, nullable)
- Description : Raison de l'intervention
- Exemple : "Vaccination systématique contre Mycoplasma", "Traitement antibiotique suite à problème respiratoire"

**`cout_total`** (decimal 8,2, nullable)
- Description : Coût de l'intervention
- Exemple : 87.50

**`notes`** (text, nullable)

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 234
type_cible: "lot"
lot_id: 45
date_evenement: "2025-06-01 10:15:00"
type_evenement: "vaccination"
produit_sanitaire_id: 3
nb_animaux_traites: 175
```

---

### Table : `evenements_alimentation`

Cette table enregistre les consommations d'aliment et les changements de plan alimentaire, principalement pour les lots (la consommation individuelle des truies est généralement gérée via leur plan d'alimentation).

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`lot_id`** (bigint, clé étrangère vers lots, nullable)
- Description : Le lot concerné

**`animal_id`** (bigint, clé étrangère vers animaux, nullable)
- Description : Pour des cas particuliers d'alimentation individuelle

**`date_debut`** (date, obligatoire)
- Description : Début de la période de consommation
- Exemple : "2025-06-01"

**`date_fin`** (date, obligatoire)
- Description : Fin de la période
- Exemple : "2025-06-07"

**`plan_alimentation_id`** (bigint, clé étrangère vers plan_alimentations, obligatoire)
- Description : Quel aliment a été distribué

**`quantite_kg`** (decimal 8,2, obligatoire)
- Description : Quantité totale consommée en kilogrammes
- Exemple : 875.50

**`nb_animaux`** (integer, nullable)
- Description : Nombre d'animaux concernés pour calculer la consommation moyenne

**`cout_total`** (decimal 8,2, nullable)
- Description : Coût de l'aliment
- Exemple : 306.43

**`notes`** (text, nullable)

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 112
lot_id: 45
date_debut: "2025-06-01"
date_fin: "2025-06-07"
plan_alimentation_id: 8
quantite_kg: 875.50
nb_animaux: 175
```

---

### Table : `pesees`

Cette table enregistre toutes les pesées, qu'elles soient individuelles (pour les reproducteurs) ou sur échantillon (pour les lots).

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`type_cible`** (enum: 'animal', 'lot', obligatoire)

**`animal_id`** (bigint, clé étrangère vers animaux, nullable)

**`lot_id`** (bigint, clé étrangère vers lots, nullable)

**`date_pesee`** (date, obligatoire)
- Description : Date de la pesée
- Exemple : "2025-06-15"

**`poids_total_kg`** (decimal 8,2, obligatoire)
- Description : Poids total mesuré. Pour un animal individuel, c'est son poids. Pour un lot, c'est le poids de l'échantillon ou du lot entier si pesée collective
- Exemple : 472.50

**`nb_animaux_peses`** (integer, défaut: 1)
- Description : Nombre d'animaux pesés. Pour un animal : 1. Pour un échantillon de lot : 20 par exemple

**`poids_moyen_kg`** (decimal 6,2, calculé)
- Description : Poids moyen par animal
- Exemple : 27.00

**`methode`** (enum: 'individuelle', 'collective', 'echantillon', obligatoire)
- Description : Comment la pesée a été réalisée

**`notes`** (text, nullable)

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 78
type_cible: "lot"
lot_id: 45
date_pesee: "2025-06-15"
poids_total_kg: 540.00
nb_animaux_peses: 20
poids_moyen_kg: 27.00
methode: "echantillon"
```

---

### Table : `mouvements`

Cette table enregistre tous les déplacements d'animaux ou de lots entre les salles. C'est important pour la traçabilité et pour gérer l'occupation des salles.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`type_cible`** (enum: 'animal', 'lot', obligatoire)

**`animal_id`** (bigint, clé étrangère vers animaux, nullable)

**`lot_id`** (bigint, clé étrangère vers lots, nullable)

**`date_mouvement`** (datetime, obligatoire)
- Description : Date et heure du mouvement
- Exemple : "2025-04-30 14:00:00"

**`salle_origine_id`** (bigint, clé étrangère vers salles, nullable)
- Description : D'où vient l'animal/lot. Peut être null si c'est une entrée dans l'élevage

**`salle_destination_id`** (bigint, clé étrangère vers salles, obligatoire)
- Description : Où va l'animal/lot

**`place_numero`** (string, 20 caractères, nullable)
- Description : Numéro de place dans la salle de destination si applicable

**`motif`** (enum: 'preparation_saillie', 'transfert_maternite', 'retour_gestantes', 'sevrage', 'transfert_engraissement', 'autre', obligatoire)
- Description : Raison du mouvement

**`nb_animaux`** (integer, défaut: 1)
- Description : Nombre d'animaux déplacés

**`notes`** (text, nullable)

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 156
type_cible: "animal"
animal_id: 42
date_mouvement: "2025-04-30 14:00:00"
salle_origine_id: 3
salle_destination_id: 1
motif: "transfert_maternite"
```

---

## 5. Les Tables de Gestion des Tâches et Alertes

Ces tables permettent de gérer le système d'alertes et de planification qui est au cœur de l'application.

### Table : `taches`

Cette table contient toutes les tâches à effectuer, qu'elles soient générées automatiquement par le système ou créées manuellement par l'utilisateur.

#### Champs détaillés

**`id`** (bigint, clé primaire)

**`titre`** (string, 200 caractères, obligatoire)
- Description : Titre de la tâche
- Exemple : "Vérifier retour en chaleurs", "Effectuer diagnostic échographie", "Injection de fer aux porcelets"

**`description`** (text, nullable)
- Description : Description détaillée de ce qu'il faut faire

**`type_tache`** (enum: 'alimentation', 'sanitaire', 'reproduction', 'mouvement', 'verification', 'autre', obligatoire)
- Description : Catégorie de la tâche

**`priorite`** (enum: 'basse', 'normale', 'haute', 'critique', défaut: 'normale')
- Description : Niveau de priorité. Les tâches critiques sont celles avec un délai strict (comme l'injection de fer dans les 3 jours)

**`type_cible`** (enum: 'animal', 'lot', 'portee', 'salle', 'general', obligatoire)
- Description : À quoi s'applique la tâche

**`animal_id`** (bigint, clé étrangère vers animaux, nullable)

**`lot_id`** (bigint, clé étrangère vers lots, nullable)

**`portee_id`** (bigint, clé étrangère vers portees, nullable)

**`salle_id`** (bigint, clé étrangère vers salles, nullable)

**`date_echeance`** (date, obligatoire)
- Description : Date limite pour accomplir la tâche
- Exemple : "2025-06-05"

**`date_debut_periode`** (date, nullable)
- Description : Pour les tâches qui ont une fenêtre de temps, la date de début. Exemple : pour "Diagnostic entre J22 et J30", date_debut serait J22 et date_echeance serait J30

**`statut`** (enum: 'en_attente', 'en_cours', 'terminee', 'annulee', défaut: 'en_attente')
- Description : État d'avancement de la tâche

**`date_realisation`** (datetime, nullable)
- Description : Quand la tâche a été accomplie

**`utilisateur_assigne_id`** (bigint, clé étrangère vers users, nullable)
- Description : À qui la tâche est assignée dans Laravel

**`utilisateur_realisation_id`** (bigint, clé étrangère vers users, nullable)
- Description : Qui a accompli la tâche

**`generee_automatiquement`** (boolean, défaut: false)
- Description : Indique si la tâche a été créée automatiquement par le système (par exemple lors de la création d'une portée) ou manuellement par un utilisateur

**`evenement_lie_type`** (string, 50 caractères, nullable)
- Description : Type d'événement qui a déclenché la tâche
- Exemple : "mise_bas", "saillie", "sevrage"

**`evenement_lie_id`** (bigint, nullable)
- Description : ID de l'événement qui a déclenché la tâche (cycle_reproduction_id, portee_id, etc.)

**`notes`** (text, nullable)

**`created_at`** et **`updated_at`**

#### Exemple de données
```
id: 445
titre: "Injection de fer aux porcelets"
type_tache: "sanitaire"
priorite: "critique"
type_cible: "portee"
portee_id: 89
date_echeance: "2025-05-10"
statut: "en_attente"
generee_automatiquement: true
```

#### Relations et logique
Quand certains événements se produisent dans l'application (comme une mise-bas), des observateurs ou des listeners Laravel créent automatiquement des tâches dans cette table avec les bonnes dates d'échéance calculées.

---

## 6. Tables Système et Utilisateurs

### Table : `users` (fournie par Laravel)

Table standard de Laravel pour les utilisateurs de l'application. Vous l'enrichirez probablement avec des champs spécifiques.

#### Champs additionnels suggérés

**`role`** (string ou relation vers une table roles)
- Description : Le rôle de l'utilisateur
- Exemple : "administrateur", "eleveur", "technicien", "veterinaire"

**`telephone`** (string, nullable)

**`created_at`** et **`updated_at`**

---

## 7. Tables de Relations Many-to-Many

Certaines relations nécessitent des tables intermédiaires.

### Table : `lot_portee` (table pivot)

Comme plusieurs portées peuvent être sevrées dans le même lot, et qu'on veut garder la traçabilité, on peut avoir besoin d'une table pivot explicite.

#### Champs

**`id`** (bigint, clé primaire)

**`lot_id`** (bigint, clé étrangère vers lots, obligatoire)

**`portee_id`** (bigint, clé étrangère vers portees, obligatoire)

**`nb_porcelets_transferes`** (integer, obligatoire)
- Description : Combien de porcelets de cette portée sont allés dans ce lot

**`poids_total_transfere_kg`** (decimal 7,2, nullable)
- Description : Poids total des porcelets de cette portée transférés

**`created_at`** et **`updated_at`**

#### Note
En réalité, avec le champ `lot_destination_id` directement dans la table portees, cette table pivot pourrait ne pas être nécessaire sauf si vous voulez permettre de répartir une même portée dans plusieurs lots différents, ce qui est rare.

---

## Schéma des Relations Principales

Pour bien visualiser comment tout s'articule, voici les relations clés.

### Circuit de Reproduction (Gestion Individuelle)

Un `Animal` (truie) a plusieurs `CyclesReproduction`

Un `CycleReproduction` a plusieurs `Saillies`

Un `CycleReproduction` produit une `Portee`

Une `Portee` est assignée à un `Lot` (le pivot !)

### Circuit de Production (Gestion Collective)

Un `Lot` contient des porcelets de plusieurs `Portees`

Un `Lot` a plusieurs `EvenementsSanitaires`

Un `Lot` a plusieurs `EvenementsAlimentation`

Un `Lot` a plusieurs `Pesees`

Un `Lot` a plusieurs `Mouvements`

### Système de Tâches

Une `Tache` peut être liée à un `Animal`, un `Lot`, une `Portee`, ou une `Salle`

Les `Taches` sont créées automatiquement lors de certains événements (mise-bas, saillie, sevrage, etc.)

---

## Considérations Techniques pour Laravel et Filament

Voici quelques recommandations importantes pour implémenter ce modèle avec Laravel douze et Filament quatre.

### Migrations

Créez vos migrations dans l'ordre logique des dépendances. Commencez par les tables de référence (races, type_salles, etc.), puis les animaux, puis les cycles, puis les lots. Utilisez les migrations Laravel avec les contraintes de clés étrangères appropriées et les index pour les performances.

### Modèles Eloquent

Chaque table aura son modèle Eloquent. Définissez bien les relations (hasMany, belongsTo, belongsToMany). Utilisez les casts pour les champs de type enum, date, datetime et decimal. Par exemple, dans le modèle Animal, vous aurez :

```php
protected $casts = [
    'date_naissance' => 'date',
    'poids_actuel_kg' => 'decimal:2',
    'statut_actuel' => StatusAnimal::class
];
```

où StatusAnimal serait un enum PHP huit point un.

### Observers et Events

C'est crucial pour votre application. Créez des observers pour les modèles clés (CycleReproduction, Portee, Lot) qui vont déclencher la création automatique de tâches. Par exemple, un observer sur Portee qui, lors de la création (created event), va automatiquement créer les tâches "Vérifier colostrum" et "Injection de fer" avec les bonnes dates.

### Scopes

Utilisez les query scopes Eloquent pour faciliter les requêtes fréquentes. Par exemple, un scope `scopeActifs` sur le modèle Lot pour récupérer uniquement les lots actifs, ou un scope `scopeGestantes` sur Animal pour récupérer les truies gestantes.

### Filament Resources

Pour Filament, vous créerez une Resource pour chaque entité principale (AnimalResource, LotResource, CycleReproductionResource, TacheResource, etc.). Utilisez les relations manager de Filament pour gérer les relations complexes (par exemple, afficher tous les cycles d'une truie dans une table relation).

### Actions personnalisées Filament

C'est là que la magie opère. Vous créerez des actions personnalisées pour des opérations complexes comme "Sevrer une portée" (qui va créer ou assigner à un lot, mettre à jour la portée, changer le statut de la truie, créer les tâches, etc.). Filament permet de créer des actions avec des formulaires modaux, parfait pour ce genre d'opérations.

### Calculs automatiques

Utilisez les accessors Eloquent ou les observers pour calculer automatiquement certains champs. Par exemple, le `poids_moyen_sevrage_kg` dans Portee peut être un accessor qui divise poids_total par nb_sevres. Ou utilisez les mutators pour mettre à jour automatiquement `nb_animaux_actuel` dans Lot quand vous enregistrez une mortalité.

### Widgets Dashboard

Avec Filament, créez des widgets pour le tableau de bord : nombre de truies gestantes, tâches en retard, lots actifs, indicateurs GTTT et GTE calculés en temps réel, etc.

### Notifications

Utilisez le système de notifications de Laravel pour alerter les utilisateurs des tâches urgentes ou en retard. Vous pouvez envoyer des notifications par email, SMS ou simplement dans l'interface Filament.

### Validation

Mettez en place des validations robustes. Par exemple, un lot ne peut pas avoir nb_animaux_actuel supérieur à nb_animaux_depart. Une date de sevrage ne peut pas être antérieure à la date de mise-bas. Utilisez les form requests Laravel pour centraliser ces validations.

---

## Conclusion

Voilà un modèle de données complet et cohérent qui respecte la logique hybride de votre application. Chaque table a un rôle précis, et les relations entre elles permettent de gérer à la fois la finesse de la gestion individuelle des reproducteurs et l'efficacité de la gestion collective des lots de production. Le système de tâches automatiques est le fil conducteur qui fait de cette application un véritable outil de pilotage plutôt qu'un simple logiciel d'enregistrement.

Ce modèle est conçu pour être implémenté avec Laravel et Filament, en tirant parti des fonctionnalités modernes d'Eloquent (relations, observers, scopes, accessors) et de Filament (resources, actions personnalisées, relation managers, widgets). La clé du succès sera dans l'automatisation intelligente des tâches et des calculs, qui transformera cette base de données en un véritable assistant pour l'éleveur.