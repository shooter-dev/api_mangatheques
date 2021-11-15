![Mangatheque Api](logo.png)

# Mangatheque Api (Django)

# Installation
Dans un premier temps, cloner le repository:
```
git clone https://github.com/shooter-dev/api_mangatheques.git
```

Installer les dépendances:
```
make install db_user=username db_password=password env=dev
```

## Usage
Lancer l'application:
```
make run
```
## Tests
Lancer la suite de tests :
```
make tests
```

## Base de données et fixtures
```
make prepare env=dev
```

## Analyse du code
Dans un premier temps, pensez à éxecuter la commande qui permet de nettoyer le code :
```
make fix
```

Lancer les outils d'analyse statique :
```
make analyse
```

# Contribuer
Veuillez prendre un moment pour lire le [guide sur la contribution](CONTRIBUTING.md).

# Changelog
[CHANGELOG.md](CHANGELOG.md) liste tous les changements effectués lors de chaque release.

# À propos
l'Api Mangatheque a été conçu initialement par [shooter-dev](https://github.com/shooter-dev).
Si vous avez le moindre question, contactez [shooter-dev](mailto:vincentbleach@gmail.com?subject=[Github]%20Api%20Mangatheque)
