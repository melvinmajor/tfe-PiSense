---
subtitle: Rapport de travail de fin d'études
date: Juin 2020
keywords: [TFE, graduation, work, PiSense, IoT, sensors, dev, report, TFE, graduation, rapport, EPHEC, 3TI]
footer-left: Rapport de TFE
toc-title: Rapport de travail de fin d'études
---

# Rapport de travail de fin d'études

_**Titre :**_ Système modulable de prises de mesures environnementales sur Raspberry Pi avec système d’affichage

_**Nom temporaire prévu pour sa commercialisation :**_ PiSense

## Introduction

Nous vivons dans un monde en perpétuelle évolution où la technologie prend une place d’importance majeure.
Les appareils connectés passent peu à peu d’une idée de conception à un produit se trouvant dans chaque foyer.
Malheureusement, il est rare de trouver à l’heure actuelle une solution tout-en-un permettant de suivre les informations environnementales directement depuis une interface web simple et efficace.

Dans un désir de suivre les informations environnementales de mon espace de vie, j'ai pris contact avec plusieurs entreprises afin de voir le potentiel derrière ce projet.

Suite à des échanges avec plusieurs clients potentiels, ce travail de fin d’études s’est peu à peu dessiné.
L’un mettait en avant l’intérêt d’obtenir des données liées à la température, l’humidité et la qualité de l’air afin d’améliorer la productivité de leurs employés.
Un autre mettait en avant la nécessité de suivre l’évolution de la présence d’un gaz spécifique dans un lieu donné afin de prévenir d’un danger pouvant s’avérer mortel.

En combinant les besoins et intérêts, les prémices de ce travail sont nées et de nouvelles questions venaient en tête : serait-il possible d’imaginer une solution de petite taille à ces demandes ? Et si cette solution se trouvait être générique afin d’être ouvert à tous ?
De plus, avec les récents évènements liés au COVID-19, une réflexion approfondie a été réalisée concernant la mesure de la qualité de l’air suite à la publication de recherches spécifique au coronavirus.

Vous découvrirez dans ce rapport la façon dont ce travail a été développé ainsi que certaines analyses réalisées.

\pagebreak

## Informations générales de ce projet

### Contexte et présentation

Dans le cadre d’une demande professionnelle demandée par la société Dreamnet SRL, un système IoT permettant le suivi de la température, de l’humidité et du taux de CO2 au sein d’une pièce est requis afin de répondre à un projet futur répondant à de nouveaux besoins.

Sur base de premiers retours avec le professeur A. Dewulf, un intérêt est également présent pour détecter de l’Oxyde de Silicium auprès d’un client disposant d’une carrière.
Cela permettra au client de prévenir de tout risque lié à une présence de l’Oxyde de Silicium à un taux trop élevé.

### But

Permettre l’optimisation de l’environnement du personnel.
Des études ont prouvé qu’une pièce à bonne température ambiante et avec une bonne qualité d’air permet d’aider à la concentration et améliorer l’état de santé ainsi que la productivité de l’être humain.

Dans le cas des retours du professeur, il s’agirait de prévenir d’un éventuel danger dans un milieu de travail à risque.

### Besoins et contraintes

Concrètement parlant, il est demandé de mettre en place une solution IoT munie de capteurs « à la carte » ainsi que de différents moyens de communication au choix pour l’utilisateur final.

_Ce choix permettra au client de définir ce dont il a besoin :_

* Wi-Fi,
* Ethernet.

Un moyen d’accès aisé aux informations est à mettre en place.
Le prix d’achat des composants pour créer l’appareil doit être le moins coûteux possible.
De ce fait, une analyse des différents composants disponible sur le marché et de leur précision à été réalisée lors de l'élaboration du cahier de charges et de l'analyse technique afin de retenir uniquement les composants ayant un bon rapport précision/prix.

Concernant les nécessités du client disposant d’une carrière, une solution entièrement sans fil est à envisager.
L’utilisation d’une batterie d’une certaine capacité pourrait s’avérer pratique à moins que le client dispose d’une prise 220V non loin de la zone où il souhaite mettre en place l’appareil.

Malheureusement, au vu des problèmes rencontré lié au confinement, les échanges avec le 2ème clients n'ont plus eu lieu.

Étant donné que l’appareil pourrait être mis en extérieur, un boîtier étanche est à étudier.

#### Contraintes

* L’appareil IoT doit rester relativement petit (de taille « acceptable »),
* L’appareil IoT et les capteurs doivent être à un prix correct (volonté de ne pas dépasser les 500€ pour le kit complet avec tous les moyens de communication éventuellement intégrés),
* La possibilité à un développeur tiers d’accéder aux données de l’appareil via une API ou un environnement Cloud (non obligatoire, mais souhaité),
* La possibilité d’une solution entièrement sans fil,
* Résistance aux intempéries si l’appareil est mis en extérieur (résistance à la pluie).

## Analyse de sécurité

En terme de sécurité, il va de soit que l'environnement web doit être sécurisé par un certificat SSL et une protection liée aux données des utilisateurs.
Dreamnet SRL conseille l'utilisation du certificat Let's Encrypt, étant un certificat gratuit et renouvelé de façon régulière.
L'augmentation de la sécurité par le biais de _Security Headers_ à implémenter sur la plateforme web est également une méthode intéressante à inclure.

Le moyen de communication de la Raspberry doit être le plus générique possible afin que même si une personne externe vienne à récolter les informations, aucune information personnelle liée à l'utilisateur ne puisse être récupéré à son insu.

## Développement

### Méthodologie

// TODO

### Raspberry Pi

Le développement a débuté avec la Raspberry Pi.
Plusieurs éléments ont été développés :

* Système de connexion à distance sécurisée par interface graphique via VNC Viewer (Raspberry Pi servant de serveur),
* Protection à la connexion par interface console par protocole SSH,
* Intégration d'un service permettant l'allongement de la durée de vie de la carte microSD,
* Développement des différents capteurs.

Concernant les capteurs, tout a été développé avec le langage de programmation Python, l'intégration d'un service de logs avec rotation et suppression automatique de l'historique ancien de plus de 30 jours.

### Site internet

Au niveau du site internet, il était originellement prévu de travailler sur une plateforme avec le framework React.JS.
J'ai rapidement pu remarquer les limites de cet environnement de développement par rapport aux besoins de ce projet.
Fin mars, j'ai entrepris la conversion complète du site internet pour travailler sur le framework Flask prévu pour Python.

Afin d'améliorer au maximum le rendement de ce framework, le modèle MVT a été suivi (ce modèle est expliqué à la suite dans un point dédié).
L'avantage de Python Flask est son intégration plus aisée de l'API ainsi que de la base de données.

Afin de comprendre au mieux l'utilisation de Flask, j'ai suivi la formation en ligne "Concevez un site avec Flask" sur OpenClassrooms.

#### Modèle MVT

Concernant la plateforme web, le modèle MVT (Modèle - Vue - Template) est suivi.

* Le modèle est la structure de l'objet dans la base de données.
  Concrètement, il est représenté par le fichier `models.py`.
* La vue décide du contenu qui sera affiché sur une page ; c'est elle qui génère le contenu à renvoyer aux requêtes adressées.
  Concrètement, il est représenté par le fichier `views.py`.
* Le template est un fichier HTML recevant des objets Python et lié à une vue.
  Concrètement, il est représenté par le dossier `templates` et tout fichier présent dans ce dernier.

#### API

Python Flask permet la mise en place d'une API (Application Programming Interface).
La documentation fournie sur le site internet officiel de Flask est relativement complète 

Dans le cadre de ce projet, j'ai suivi la formation en ligne "Adoptez les API REST pour vos projets web" sur OpenClassrooms.
Cette formation m'a permis de mieux visualiser les concepts théoriques derrière les API et ainsi orienter la programmation de ce dernier.

### Base de données

Lors du choix de technologies, il était décidé de partir sur MariaDB car jugé plus intéressant en termes de fonctionnalités, stabilité et de la licence Open SQL Server en comparaison de MySQL.
Malheureusement, au vu de certaines incompatibilités rencontrées avec MariaDB lors de l'implémentation de l'environnement web, SQLite a été le choix final.

Concrètement parlant, la base de données comporte 2 tables :

* User
  Elle contient toutes les informations liées à l'utilisateur. Nous y trouverons son nom et prénom, adresse e-mail, mot de passe (qui sera protégé), date d'inscription et s'il est en possession d'un appareil de suivi ainsi que de quel(s) capteur(s).
* Box
  Elle contient les informations liées à la Raspberry Pi et les capteurs. Nous y trouverons les différentes informations environnementales tout comme l'ID de l'appareil ainsi que l'horodatage de la prise de mesure.

\pagebreak

## Problèmes rencontrés

### Confinement

Avec cette période exceptionnelle que nous rencontrons cette année via la présence de mesures strictes de confinement afin de contrer le coronavirus, cela a eu un impact non négligeable sur l'avancée de ce TFE.

En effet, avec le stage en télétravail, les quelques semaines de stages suspendus qui sont a rattraper et la distanciation sociale, j'ai dû réadapter à plusieurs reprises mon planning de travail.
Vivant seul, j'ai ressenti la distanciation sociale de façon conséquente.

De plus, je n'ai eu que très peu d'échanges avec les clients concernant mon TFE faisant que le développement n'a pu être avancé principalement que sur base des notes prises lors d'entrevues avant les mesures prises par le gouvernement.

### Incompatibilité entre les technologies choisies

Au début du projet, sur base des demandes du client et d'une analyse amenant à l'élaboration d'un cahier de charges, certains choix de technologies m'avaient semblé évidents.
C'était sans compter la complexité de mise en place du framework React.JS au niveau de l'environnement web.

Une première réadaptation des choix technologiques à été faite en quittant le framework React.JS au profit du framework Flask.

Malheureusement, des soucis de connexion entre Python Flask et la base de données MariaDB se sont présentés très rapidement, nécessitant le changement vers SQLite qui lui, ne pose aucun problème d'interaction par le biais du module SQLAlchemy utilisé par Flask pour interagir avec une base de données.

\pagebreak

## Piste d'amélioration

// TODO

\pagebreak

## Conclusion

// TODO

\pagebreak

## Bibliographie

// TODO
