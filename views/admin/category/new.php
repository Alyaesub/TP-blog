<?php
//feuille de code admin pour la création d'une nouvelle catégorie 
use App\Model\Category;
use App\ConnexionDb;

$pdo = ConnexionDb::getPdo();
$title = "Créer une nouvelle catégorie";

// Si le formulaire est soumis, on insère la catégorie dans la base de données 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $slug = $_POST['slug'] ?? '';

  // Insertion de la catégorie dans la table category
  $query = $pdo->prepare("INSERT INTO category (name, slug) VALUES (?, ?)");
  $query->execute([$name, $slug]);
  $categoryId = $pdo->lastInsertId();


  header('Location: ' . $router->url('admin_category')); //redirection vers la page admin catégories
  exit();
}

// Récupération des catégories pour le select
$categories = $pdo->query("SELECT * FROM category ORDER BY name ASC")->fetchAll(PDO::FETCH_CLASS, \App\Model\Category::class);
?>

<div class="container">
  <h1>Créer une nouvelle catégorie</h1>
  <!-- Formulaire pour créer une nouvelle catégorie -->
  <form action="<?= $router->url('admin_category_create') ?>" method="POST">
    <div class="mb-3">
      <label for="name" class="form-label">Nom de la catégorie</label>
      <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="mb-3">
      <label for="slug" class="form-label">Slug</label>
      <input type="text" class="form-control" id="slug" name="slug" required>
    </div>

    <div class="d-flex justify-content-between">
      <a href="<?= $router->url('admin_category_new') ?>" class="btn btn-secondary">Retour</a>
      <button type="submit" class="btn btn-primary">Créer la catégorie</button>
    </div>
  </form>
</div>