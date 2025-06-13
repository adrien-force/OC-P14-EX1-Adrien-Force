<img src="assets/images/logo.png" alt="CritiPixel" width="200" />

<h1 align="center">CritiPixel</h1>
<p align="center"><i>Projet N°14 de la formation Développeur d'application PHP Symfony
@OpenClassrooms <br> <a href="https://github.com/adrien-force/876-p14-critipixel/commits?author=adrien-force"><img src="https://img.shields.io/badge/Auteur_:-Adrien_FORCE-orange"></a></i></p>

## 🎯 Table des matières
- [Description du projet](#-description)
- [Améliorations apportées](#-améliorations-apportées)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Utilisation](#-utilisation)

## 📄 Description
<br>

CritiPixel est une plateforme de critiques de jeux vidéo développée avec Symfony 6. Ce projet étend le projet scolaire initial avec des fonctionnalités supplémentaires, des tests améliorés et une intégration CI/CD.

## 🚀 Améliorations apportées

- **Tests unitaires** : Couverture de test complète pour la logique métier
- **Tests fonctionnels** : Tests des flux utilisateurs critiques
- **Intégration CI/CD** : Pipeline automatisé de test et linting
- **Qualité de code** : Intégration de PHPStan et PHP-CS-Fixer
- **Support Docker** : Environnement de développement conteneurisé
- **Makefile** : Commandes simplifiées de gestion du projet

## 🔧 Prérequis

- PHP >= 8.2
- Composer
- Extension PHP Xdebug
- Symfony CLI
- Docker et Docker Compose (optionnel)

## 🛠️ Installation

1. Cloner le projet sur votre machine
```bash
git clone https://github.com/adrien-force/876-p14-critipixel.git
cd critipixel
```

2. Installer le projet
```bash
make install
```

Cette commande va :
- Nettoyer les fichiers existants
- Reconstruire les conteneurs Docker
- Installer les dépendances
- Configurer la base de données
- Charger les fixtures
- Vider et réchauffer le cache
- Demarrer le serveur Symfony

Note : Si tout va bien les ports du container de la base de données sont valides, sinon il faudra les changer dans le fichier .env.local en les remplaçant par les ports affichés par `docker ps`


Le site est maintenant disponible à l'adresse : <http://127.0.0.1:8000>

## 🔥️ Utilisation

### Commandes Make disponibles

- `make clean` - Nettoyer les fichiers générés
- `make reinstall` - Réinstaller le projet sans Docker
- `make reinstall-docker` - Réinstallation complète avec Docker
- `make test` - Exécuter la suite de tests
- `make qa` - Exécuter les vérifications de qualité de code
- `make cs-fix` - Corriger le style de code
- `make phpstan` - Exécuter l'analyse statique

### Développement

#### Exécuter les tests
```bash
make test
```

#### Qualité de code
```bash
make qa
```

#### Correction du style de code
```bash
make cs-fix
```

#### Analyse statique
```bash
make phpstan
```

## 📚 Documentation originale du projet

## Configuration

### Base de données
Actuellement, le fichier `.env` est configuré pour la base de données PostgreSQL mise en place dans `docker-compose.yml`.
Cependant, vous pouvez créer un fichier `.env.local` si nécessaire pour configurer l'accès à la base de données.
Exemple :
```dotenv
DATABASE_URL=mysql://root:Password123!@host:3306/criti-pixel
```

### PHP (optionnel)
Vous pouvez surcharger la configuration PHP en créant un fichier `php.local.ini`.

De même pour la version de PHP que vous pouvez spécifier dans un fichier `.php-version`.

## Usage

### Base de données

#### Supprimer la base de données
```bash
symfony console doctrine:database:drop --force --if-exists
```

#### Créer la base de données
```bash
symfony console doctrine:database:create
```

#### Exécuter les migrations
```bash
symfony console doctrine:migrations:migrate -n
```

#### Charger les fixtures
```bash
symfony console doctrine:fixtures:load -n --purge-with-truncate
```

*Note : Vous pouvez exécuter ces commandes avec l'option `--env=test` pour les exécuter dans l'environnement de test.*

### SASS

#### Compiler les fichiers SASS
```bash
symfony console sass:build
```
*Note : le fichier `.symfony.local.yaml` est configuré pour surveiller les fichiers SASS et les compiler automatiquement quand vous lancez le serveur web de Symfony.*

### Tests
```bash
symfony php bin/phpunit
```

*Note : Penser à charger les fixtures avant chaque éxécution des tests.*

### Serveur web
```bash
symfony serve
```