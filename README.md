# Blog PHP Vanilla

Un blog développé en PHP vanilla avec une architecture MVC.

-   Diagramme de classe uml :
    lien figma : https://www.figma.com/design/xnm89zb5NGNMmBjYYhWCyp/diagramme-uml-blog?t=NdLBFvM1FvzMnmuC-0

-   Diagramme MCD :
    lien figma : https://www.figma.com/design/DKLz223VTwV9UuBwUy6ZxO/diagramme-MCD-blog?node-id=0-1&p=f&t=NdLBFvM1FvzMnmuC-0

## 🚀 Fonctionnalités

## 🔐 Authentification

L'espace d'administration est protégé par une authentification.

> Ces identifiants sont valables uniquement en environnement de développement.

### Frontend

-   [x] Page d'accueil avec liste des articles (pagination)
-   [x] Page de catégorie avec liste des articles (pagination)
-   [x] Page de détail d'un article

### 🧠 Backend

-   **PHP Vanilla** : langage principal utilisé côté serveur
-   **Composer** : gestionnaire de dépendances PHP
-   **AltoRouter** : système léger de routage
-   **Whoops** : gestion améliorée des erreurs et affichage des stacktraces
-   **Faker** : génération de données factices pour les tests

### 🧰 Outils de développement

-   **Whoops** : affichage clair des erreurs en environnement de développement
-   **Faker** : création de jeux de données aléatoires pour simuler des articles et des catégories
-   **Adminer** : outil simple et léger pour manipuler la base de données MySQL

### Frontend

-   Bootstrap Quitstarter (Framework CSS)

### Base de données

-   MySQL
-   Adminer (Interface d'administration de base de données)

## 📁 Structure du projet

```
blog/
├── commands/
│   └── fill/         # Scripts de génération de données
├── config/           # Fichiers de configuration
├── public/           # Point d'entrée de l'application
├── src/              # Code source
│   ├── Controllers/  # Contrôleurs
│   ├── Models/       # Modèles
│   └── Views/        # Vues
└── vendor/           # Dépendances
```

## 🚀 Installation

1. Cloner le repository

```bash
git clone [URL_DU_REPO]
```

2. Installer les dépendances

```bash
composer install
```

3. Configurer la base de données

-   Créer une base de données MySQL
-   Importer le schéma de la base de données
-   Configurer les paramètres de connexion dans `config/database.php`

4. Générer des données de test (optionnel)
   -création de bdd avec faker

```bash
php commands/fill/generate.php
```

## 📝 Base de données

### Tables principales

-   `articles` : Stockage des articles
-   `categories` : Gestion des catégories
-   `users` : Gestion des utilisateurs

## 🔄 État d'avancement

-   [x] Mise en place de l'architecture
-   [x] Configuration de l'environnement
-   [x] Création de la structure de la base de données
-   [x] Implémentation des fonctionnalités frontend
-   [x] Implémentation des fonctionnalités backend
-   [x] Tests et débogage
-   [x] Documentation finale

## ✅ Tests

Le projet a été testé manuellement sur un environnement local (PHP server ou Apache).  
Des jeux de données aléatoires sont générés avec Faker pour valider les interfaces et le back-office.

## 🚀 Déploiement

Le projet peut être déployé sur un serveur PHP/MySQL classique (Apache recommandé).

-   Uploadez le contenu de `public/` dans le dossier public de votre serveur
-   Modifiez le fichier `config/database.php` selon votre hébergement
-   Importez la base de données via Adminer ou phpMyAdmin

## 👥 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou à soumettre une pull request.
