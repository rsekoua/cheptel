# Cahier des Charges Reformulé : Application de Gestion d'Élevage Porcin

## 1. Vue d'ensemble du projet

Vous allez développer une application web qui fonctionne comme un tableau de bord de pilotage pour un élevage de porcs. Imaginez cette application comme un système de gestion qui suit la vie de chaque animal depuis sa naissance jusqu'à sa sortie de l'élevage, tout en générant automatiquement des rappels pour les tâches à accomplir et en calculant des indicateurs de performance pour aider l'éleveur à prendre les bonnes décisions.

## 2. Le principe fondamental : Deux modes de gestion complémentaires

L'application doit gérer deux types d'animaux de manière très différente, et c'est ce qui fait toute sa complexité. Comprendre cette distinction est essentiel.

### Premier mode : La gestion individuelle (pour les animaux reproducteurs)

Ce mode concerne les animaux qui servent à la reproduction. Chaque animal a une identité propre, comme une fiche patient dans un cabinet médical. Pour ces animaux, on suit tout individuellement : quand ils se sont reproduits, avec qui, quand ils ont donné naissance, combien de petits ils ont eu, quels soins ils ont reçus, etc.

Les animaux concernés sont les truies (femelles reproductrices), les cochettes (jeunes femelles qui deviendront des truies) et les verrats (mâles reproducteurs).

Imaginez que vous gérez une écurie de chevaux de course : vous connaissez le nom de chaque cheval, son pedigree, ses performances individuelles. C'est pareil ici.

### Deuxième mode : La gestion collective (pour les animaux de production)

Une fois que les porcelets sont sevrés (c'est-à-dire séparés de leur mère), ils perdent leur identité individuelle. On ne les suit plus un par un, mais par groupe qu'on appelle "Lot". Un lot peut contenir des dizaines ou des centaines d'animaux qu'on gère ensemble.

Pour comprendre l'intérêt : imaginez une usine qui fabrique des pièces. Une fois les pièces produites, on ne suit plus chaque pièce individuellement, on suit des palettes ou des containers entiers. C'est la même logique ici.

### Le moment pivot : Le sevrage

Le sevrage est le moment critique où l'application doit faire la transition entre ces deux modes. C'est comme un carrefour dans le système : avant le sevrage, on est en mode individuel (on suit chaque truie et sa portée), après le sevrage, on passe en mode collectif (on regroupe les porcelets de plusieurs mères pour former des lots).

## 3. Le concept de "Bande" (pour la gestion individuelle)

Même si on gère les truies individuellement, l'éleveur les organise en "bandes". Une bande, c'est un groupe de truies qu'on fait se reproduire à peu près en même temps. Par exemple, la "Bande Semaine 10 de 2025" pourrait contenir vingt truies qu'on va essayer d'inséminer toutes pendant la même semaine.

Pourquoi faire ça ? Pour des raisons pratiques. Si toutes les truies d'une bande mettent bas en même temps, ça permet de mieux organiser les salles de maternité, de sevrer tous les porcelets ensemble, et de créer des lots homogènes.

Attention : même si les truies sont dans la même bande, elles restent suivies individuellement. Certaines peuvent avoir leurs chaleurs le lundi, d'autres le jeudi. L'application doit permettre de voir à la fois la performance individuelle de chaque truie ET la performance globale de la bande.

## 4. Les cinq grands parcours de vie dans l'application

Maintenant que vous comprenez les principes, voici comment l'application doit gérer concrètement le cycle de vie des animaux, découpé en cinq flux principaux.

### FLUX 1 : L'arrivée et la préparation d'une jeune femelle (Cochette)

#### Point de départ

Une jeune femelle arrive dans l'élevage. On l'appelle une "cochette" tant qu'elle n'a pas encore eu sa première mise-bas.

#### Ce que l'application doit faire

L'utilisateur enregistre cette nouvelle cochette dans le système avec son identité. L'application commence à suivre automatiquement son âge et son poids.

Le but est d'amener cette jeune femelle à être prête pour sa première reproduction. Pour cela, elle doit atteindre environ huit mois d'âge et peser environ cent quarante kilogrammes. C'est comme un compteur qui avance progressivement.

L'application doit générer automatiquement des alertes pour prévenir l'utilisateur :

Dès que la cochette atteint environ cent quatre-vingts jours (six mois), l'application alerte pour commencer la "stimulation par verrat". Concrètement, on met la jeune femelle au contact d'un mâle (sans accouplement) pour déclencher ses hormones et ses cycles de reproduction. C'est une technique d'élevage standard.

L'application alerte aussi pour les vaccinations obligatoires (contre des maladies spécifiques aux porcs comme la parvovirose).

Quand l'éleveur détecte les premières chaleurs de la cochette (c'est un comportement observable : la femelle se fige quand on appuie sur son dos), il l'enregistre dans l'application.

#### Résultat

La cochette est maintenant prête à devenir une truie reproductrice. Elle passe au flux suivant.

### FLUX 2 : Le cycle de reproduction d'une truie

C'est le cœur du système pour les animaux reproducteurs. Ce cycle va se répéter plusieurs fois dans la vie d'une truie (une truie peut avoir cinq à dix cycles de reproduction dans sa carrière).

#### Point de départ

Une truie est au repos après un sevrage (elle vient de sevrer ses porcelets) OU c'est une cochette qui vient d'avoir ses premières chaleurs. Son statut dans l'application est "Sevrée" ou "Prête".

#### Première phase : Déclenchement d'un nouveau cycle

Dès que la truie est au repos, l'application crée automatiquement un nouvel enregistrement qu'on appelle "CycleReproduction". C'est comme ouvrir un nouveau dossier pour cette tentative de reproduction.

L'application génère immédiatement des tâches automatiques :

Modifier le plan d'alimentation de la truie en mode "Flushing". Le flushing, c'est une technique où on augmente temporairement la quantité de nourriture pour stimuler l'ovulation. C'est prouvé scientifiquement que ça améliore les résultats.

Commencer la stimulation quotidienne par contact avec un verrat (pour accélérer le retour en chaleurs).

#### Deuxième phase : Détection des chaleurs et insémination

L'éleveur observe ses truies chaque jour. Quand il détecte les chaleurs (le fameux réflexe d'immobilisation), il l'enregistre dans l'application avec la date et l'heure précises.

Ensuite, l'éleveur procède à l'insémination. Il y a deux méthodes possibles :

L'insémination artificielle (IA) : on utilise de la semence congelée ou fraîche d'un verrat. C'est la méthode la plus courante aujourd'hui car elle permet de meilleures performances génétiques.

La monte naturelle (MN) : on met la truie en présence d'un verrat qui la saillit naturellement.

Pour chaque insémination, l'utilisateur enregistre dans l'application :

- L'identité de la truie concernée
- La date et l'heure exactes
- Le type (IA ou MN)
- Si c'est une IA : le numéro du lot de semence utilisé
- Si c'est une MN : l'identité du verrat utilisé

Important : pour maximiser les chances de réussite, on fait souvent plusieurs inséminations pendant les mêmes chaleurs. Par exemple, une première le jour un à quatorze heures, une deuxième le jour deux à dix heures. L'application doit permettre d'enregistrer ces multiples inséminations pour le même cycle.

#### Troisième phase : La gestation (attente et confirmation)

Dès que la première insémination est enregistrée, l'application considère que la truie est potentiellement gestante. Cette date d'insémination devient le "Jour Zéro" de la gestation. Le statut de la truie passe à "Gestante (en attente de confirmation)".

Maintenant, l'application entre en mode surveillance active et génère des alertes automatiques :

Entre le jour dix-huit et le jour vingt-trois après l'insémination : alerte pour surveiller si la truie revient en chaleurs. Si elle revient en chaleurs, ça signifie que l'insémination a échoué.

Entre le jour vingt-deux et le jour trente : alerte pour faire une échographie de diagnostic. C'est le moment optimal pour confirmer la gestation.

Quand l'éleveur fait l'échographie, il enregistre le résultat dans l'application :

Si le diagnostic est POSITIF (truie gestante) : le statut du cycle est confirmé à "Gestante". On continue le flux.

Si le diagnostic est NÉGATIF (pas de gestation) : le cycle de reproduction en cours est marqué comme "Échoué". La truie retourne au statut "Sevrée" et on recommence tout depuis le début du Flux 2. C'est un échec mais c'est normal, ça arrive.

#### Quatrième phase : Suivi de la gestation

Une gestation de truie dure environ cent quatorze à cent seize jours (un peu moins de quatre mois). Pendant toute cette période, l'application doit gérer automatiquement plusieurs aspects.

Changement automatique du plan d'alimentation : l'application bascule la truie sur un régime "Gestation en trois phases". Pourquoi trois phases ? Parce que les besoins nutritionnels de la truie évoluent :

- Phase un (jours zéro à trente) : ration modérée pour sécuriser l'implantation des embryons
- Phase deux (jours trente à quatre-vingt-cinq) : ration réduite pour éviter que la truie grossisse trop
- Phase trois (jours quatre-vingt-cinq à cent quinze) : ration augmentée pour préparer la lactation

L'application doit calculer automatiquement la ration quotidienne en fonction du jour de gestation où se trouve la truie.

Alertes pour les vaccinations : vers la fin de la gestation, l'application génère des alertes pour vacciner la truie contre des maladies qui pourraient affecter les porcelets (E. coli, clostridies). Ces vaccins protègent indirectement les futurs porcelets via le colostrum.

Alerte de transfert en maternité : environ sept jours avant la date prévue de mise-bas, l'application génère une alerte pour transférer la truie dans une salle spéciale appelée "salle de maternité". Ce sont des boxes individuels équipés pour la mise-bas et l'allaitement.

### FLUX 3 : La mise-bas et l'allaitement en maternité

#### Point de départ

La truie est dans sa case de maternité, on approche de la date prévue de mise-bas.

#### Préparation de la salle

Avant que la truie arrive, l'application présente à l'utilisateur des check-lists de préparation :

- La salle a-t-elle été nettoyée et désinfectée (ce qu'on appelle le "vide sanitaire") ?
- La température est-elle correcte (vingt-deux degrés pour la truie, trente degrés dans le nid chauffant pour les porcelets) ?
- Les équipements sont-ils fonctionnels ?

#### L'événement de mise-bas

Quand la truie met bas, l'utilisateur enregistre cet événement majeur dans l'application avec toutes les informations importantes :

- La date et l'heure de mise-bas
- Le nombre de porcelets nés vivants
- Le nombre de mort-nés (ça arrive malheureusement)
- Le nombre de momifiés (embryons qui sont morts pendant la gestation)

À ce moment précis, l'application crée automatiquement un nouvel enregistrement appelé "Portée", qui est lié au cycle de reproduction en cours. La portée représente l'ensemble des porcelets issus de cette mise-bas, qu'on va suivre ensemble tant qu'ils restent avec leur mère.

#### Les soins critiques aux porcelets (gérés par l'application)

Dès que la portée est créée, l'application génère automatiquement des tâches urgentes avec des délais stricts :

Dans les douze premières heures (Jour Zéro) : alerte pour vérifier que tous les porcelets ont bien pris le colostrum (le premier lait, riche en anticorps). Sans colostrum, les porcelets ont très peu de chances de survivre.

Entre le jour un et le jour trois : alerte pour faire l'injection de fer à chaque porcelet. Les porcelets naissent avec très peu de réserves de fer et développeraient une anémie mortelle sans cette injection. C'est systématique dans tous les élevages modernes.

#### Gestion de la lactation de la truie

Automatiquement à la création de la portée, l'application bascule le plan d'alimentation de la truie en mode "Lactation". Une truie qui allaite a des besoins énergétiques énormes (elle doit produire du lait pour dix à quinze porcelets affamés). Souvent, on la nourrit "à volonté" pendant cette période.

La lactation dure généralement entre vingt-et-un et vingt-huit jours. C'est une période où les porcelets grandissent très vite en tétant leur mère.

#### Résultat

Au bout de trois à quatre semaines, les porcelets sont assez forts pour être sevrés. On arrive au moment pivot de toute l'application.

### FLUX 4 : Le sevrage (LA GRANDE TRANSITION)

C'est LE moment clé de l'application, celui qui fait le pont entre la gestion individuelle et la gestion collective. Comprenez bien cette étape car c'est elle qui structure toute la logique du système.

#### Le contexte du sevrage

Le sevrage, c'est le moment où on sépare les porcelets de leur mère. En élevage moderne, ça se fait généralement par "vague" : l'éleveur sèvre toutes les truies d'une même bande en même temps (ou sur quelques jours). Par exemple, il va sevrer toutes les portées nées pendant la semaine quarante.

Pourquoi sevrer en groupe ? Parce que ça permet de créer des lots homogènes de porcelets du même âge, et de libérer toute une salle de maternité d'un coup pour la nettoyer avant d'accueillir la prochaine bande.

#### L'action de sevrage dans l'application

L'utilisateur sélectionne plusieurs portées (ou plusieurs truies allaitantes) qui vont être sevrées ensemble. Il active l'action "Sevrer" qui est une action groupée.

À ce moment, l'application doit proposer à l'utilisateur deux options :

Option un : Assigner ces portées à un lot de porcelets existant (si on veut ajouter ces porcelets à un lot déjà créé).

Option deux : Créer un nouveau lot. Dans ce cas, l'utilisateur donne un nom au lot (par exemple "Lot Post-Sevrage Semaine 44 de 2024" ou "Lot PS-S44-24" en abrégé) et spécifie que c'est un lot de type "Post-Sevrage".

#### Les enregistrements pour chaque portée

Pour chaque portée qu'on sèvre, l'utilisateur doit saisir dans l'application :

- Le nombre exact de porcelets sevrés (certains ont pu mourir pendant la lactation, donc ce nombre peut être inférieur au nombre de nés vivants)
- Le poids total de la portée à ce moment (on pèse tous les porcelets ensemble)

L'application enregistre alors vers quel lot ces porcelets sont transférés. C'est crucial : c'est le lien qui permettra plus tard de tracer l'origine des animaux dans un lot.

#### Les conséquences pour la truie

Du côté de la truie mère, le sevrage marque la fin de son cycle de reproduction actuel. L'application marque le cycle de reproduction comme "Terminé" avec succès. Le statut de la truie redevient "Sevrée".

Et que se passe-t-il ensuite pour la truie ? Elle recommence tout le Flux 2 depuis le début ! C'est un cycle qui se répète. La truie va se reposer quelques jours, revenir en chaleurs, être inséminée à nouveau, mener une nouvelle gestation, avoir une nouvelle portée, etc. Une truie performante peut faire cela cinq à dix fois dans sa carrière.

#### La transition pour les porcelets

C'est ici que la magie opère : les porcelets qui étaient suivis individuellement comme "portées" (groupe de frères et sœurs nés de la même truie) deviennent des membres anonymes d'un "Lot" (groupe de dizaines ou centaines d'animaux de même âge).

À partir de maintenant, on ne sait plus quel porcelet vient de quelle truie. On ne les suit plus individuellement. On gère le lot dans son ensemble. C'est la bascule du système.

### FLUX 5 : Le post-sevrage et l'engraissement (gestion collective)

À partir du sevrage, l'entité principale n'est plus l'animal individuel, mais le "Lot". Un lot peut contenir cinquante, cent, deux cents porcelets ou plus. Tout ce qu'on fait maintenant concerne le lot entier.

#### Phase de post-sevrage (les premières semaines après sevrage)

C'est une période critique et délicate. Les porcelets viennent d'être séparés de leur mère, ils sont stressés, leur système digestif doit s'adapter à une alimentation solide. Ils sont fragiles.

L'application génère automatiquement des tâches pour le lot :

Assigner le plan d'alimentation "Post-Sevrage Premier Âge". C'est une alimentation spéciale, hyper-digestible, très coûteuse mais nécessaire pour éviter les diarrhées et la mortalité. On utilise cet aliment pendant deux à trois semaines.

Générer les alertes de vaccination pour le lot (vaccins contre les maladies respiratoires et digestives).

#### Les événements collectifs sur le lot

À partir de maintenant, tous les événements sont enregistrés au niveau du lot, plus au niveau de l'animal individuel :

Événements sanitaires : "Vaccination contre X effectuée sur le Lot PS-S44-24 le 15 novembre"

Événements alimentaires : "Consommation de cinq cents kilogrammes d'aliment Y par le Lot PS-S44-24 entre le 1er et le 7 novembre"

Événements de pesée : Pour évaluer la croissance, on ne peut pas peser tous les animaux. On pèse un échantillon (dix ou vingt animaux du lot) et on enregistre ça pour calculer le GMQ (Gain Moyen Quotidien, c'est-à-dire de combien de grammes les animaux grossissent par jour en moyenne).

#### Transfert en salle d'engraissement

Après quelques semaines en post-sevrage, quand les porcelets atteignent environ vingt-cinq à trente kilogrammes, on les transfère dans des salles d'engraissement plus grandes. L'utilisateur déplace le lot entier dans l'application, ce qui met à jour le statut et la localisation du lot.

À ce moment, l'application modifie automatiquement le plan d'alimentation vers "Engraissement Croissance". C'est un aliment différent, moins coûteux, adapté à des animaux plus robustes.

#### Phase de finition

Quand les porcs approchent du poids de vente (généralement entre cent et cent vingt kilogrammes), l'application bascule automatiquement vers un aliment "Finition". Cet aliment a une composition spécifique pour optimiser la qualité de la viande.

#### Sortie du lot (vente à l'abattoir)

C'est le moment final du cycle de production. L'éleveur vend le lot à un abattoir. Il enregistre dans l'application :

- La date de sortie
- Le nombre exact d'animaux vendus (il peut y avoir eu quelques pertes pendant l'engraissement)
- Le poids total du lot à la sortie

L'application calcule alors automatiquement tous les indicateurs de performance de ce lot :

- Poids moyen par animal
- Taux de mortalité depuis le sevrage
- Gain moyen quotidien pendant toute la période d'engraissement
- Indice de consommation (combien de kilogrammes d'aliment ont été nécessaires pour produire un kilogramme de viande)

Le lot est alors archivé dans le système. Le cycle de production est complet.

---

## 5. Synthèse : Comment tout s'articule

Pour bien comprendre l'application dans son ensemble, visualisez-la comme deux circuits parallèles qui se rejoignent au moment du sevrage :

### Circuit A (les mères)

Les truies tournent en boucle dans les Flux 1, 2, 3 et retour au Flux 2. Chaque truie refait ce cycle plusieurs fois dans sa carrière. C'est une gestion individuelle fine avec beaucoup d'événements et d'alertes spécifiques à chaque animal.

### Circuit B (les produits)

Les porcelets naissent dans le circuit A (lors de la mise-bas), puis au moment du sevrage, ils basculent dans le Flux 4 et 5 où ils sont gérés collectivement par lots jusqu'à leur sortie. C'est une gestion de masse, avec des événements et un suivi au niveau du groupe.

### Le carrefour central

Le sevrage est le carrefour qui connecte ces deux circuits. C'est pour ça qu'il doit être particulièrement bien géré dans l'application, avec une interface claire qui permet de créer ou d'assigner des lots facilement.

L'application doit aussi générer en permanence des alertes intelligentes basées sur les dates (dates de gestation, dates de vaccination, dates de transfert) et calculer automatiquement les indicateurs de performance pour aider l'éleveur à piloter son exploitation efficacement.

---

## 6. Les fonctionnalités transversales attendues

Au-delà des cinq flux principaux, l'application doit offrir des fonctionnalités transversales qui permettent une gestion complète et efficace de l'élevage.

### Système d'alertes intelligent

L'application doit générer automatiquement des alertes et des rappels basés sur les événements et les délais. Ces alertes doivent être contextuelles, pertinentes et priorisées selon leur urgence. Par exemple, une alerte pour une vaccination critique doit être plus visible qu'un rappel de routine.

### Plans d'alimentation automatisés

Le système doit gérer automatiquement les plans d'alimentation en fonction du statut de l'animal ou du lot. Quand une truie passe de "sevrée" à "gestante", son plan d'alimentation doit basculer automatiquement. Quand un lot passe de "post-sevrage" à "engraissement", même logique. Ces changements doivent être fluides et calculés en temps réel.

### Calcul des indicateurs de performance

L'application doit calculer en continu les indicateurs techniques et économiques essentiels pour la gestion de l'élevage. Cela inclut le taux de fertilité des truies, le nombre moyen de porcelets sevrés par truie par an, les gains moyens quotidiens, les indices de consommation, les taux de mortalité par phase, et les marges économiques par lot ou par bande.

### Traçabilité complète

À tout moment, l'utilisateur doit pouvoir retracer l'historique complet d'un animal reproducteur ou d'un lot. Pour une truie, on doit pouvoir voir tous ses cycles de reproduction, toutes ses portées, toutes ses performances. Pour un lot, on doit pouvoir remonter jusqu'aux truies d'origine de tous les porcelets qui le composent.

### Gestion des salles et des emplacements

L'application doit permettre de gérer les différentes salles de l'élevage (maternité, post-sevrage, engraissement) et les emplacements disponibles. Quand on déplace un animal ou un lot, l'application doit vérifier la disponibilité, mettre à jour les occupations, et générer les alertes nécessaires pour la préparation des salles.

### Interface adaptée aux deux modes de gestion

L'interface utilisateur doit clairement distinguer les sections de gestion individuelle (reproducteurs) et de gestion collective (lots), tout en permettant une navigation fluide entre les deux quand nécessaire, notamment au moment du sevrage.

---

## 7. Objectifs techniques et de performance

### Objectifs GTTT (Gestion Technique du Troupeau de Truies)

L'application doit calculer et présenter les indicateurs suivants pour évaluer la performance technique du troupeau de reproductrices :

- Taux de fertilité (pourcentage de truies qui deviennent gestantes après insémination)
- Taux de mise-bas (pourcentage de truies gestantes qui mettent effectivement bas)
- Nombre moyen de porcelets nés vivants par portée
- Nombre moyen de porcelets sevrés par portée
- Taux de mortalité en maternité
- Intervalle moyen entre sevrage et insémination
- Intervalle moyen entre mise-bas (durée d'un cycle complet)
- Nombre de portées par truie et par an

### Objectifs GTE (Gestion Technico-Économique)

L'application doit calculer et présenter les indicateurs économiques clés :

- Coût alimentaire par porcelet sevré
- Coût alimentaire par kilogramme de viande produit
- Indice de consommation global (kilogrammes d'aliment pour produire un kilogramme de gain de poids)
- Marge brute par truie productive par an
- Marge brute par lot vendu
- Coût de production par kilogramme de porc vif

Ces indicateurs doivent être calculés automatiquement par l'application en se basant sur les données saisies tout au long du processus de production.

---

## Conclusion

Ce cahier des charges reformulé vous donne une vision claire et complète du fonctionnement attendu de l'application. Les cinq flux principaux constituent le squelette du système, avec le sevrage comme point pivot central. Autour de ces flux, l'application doit offrir des fonctionnalités intelligentes d'alertes, de calculs automatiques et de traçabilité pour permettre à l'éleveur de piloter efficacement son exploitation et d'optimiser ses performances techniques et économiques.

L'enjeu principal du développement sera de bien gérer la transition entre gestion individuelle et gestion collective, tout en maintenant la traçabilité et en automatisant au maximum les tâches répétitives pour libérer du temps à l'éleveur et lui permettre de se concentrer sur les décisions stratégiques.