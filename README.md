# Blog PHP Vanilla

Un blog développé en PHP vanilla avec une architecture MVC.

admin: alya@test.com
password: password123

## 🚀 Fonctionnalités

### Frontend

-   [x] Page d'accueil avec liste des articles (pagination)
-   [x] Page de catégorie avec liste des articles (pagination)
-   [x] Page de détail d'un article

### Backend (Administration)

-   [x] Gestion des catégories (CRUD)
-   [x] Gestion des articles (CRUD)

## 🛠️ Technologies utilisées

### Backend

-   PHP Vanilla
-   Composer (Gestionnaire de dépendances)
-   Altorouter (Gestion des routes)
-   Whoops (Outils de débogage)
-   Faker.php (Génération de données factices)

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
-   [ ] Tests et débogage
-   [ ] Documentation finale

## 📚 Documentation

La documentation détaillée sera disponible dans le dossier `docs/` une fois le projet terminé.

## 👥 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou à soumettre une pull request.
