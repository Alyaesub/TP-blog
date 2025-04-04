<?php
//feuille de code admin pour la création d'un nouvel article 
use App\Model\Post;
use App\ConnexionDb;

$pdo = ConnexionDb::getPdo();
$title = "Créer un nouvel article";

// Si le formulaire est soumis, on insère l'article dans la base de données 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $slug = $_POST['slug'] ?? '';
  $content = $_POST['content'] ?? '';
  $categories = $_POST['categories'] ?? [];

  // Insertion de l'article dans la table post
  $query = $pdo->prepare("INSERT INTO post (name, slug, content, created_at) VALUES (?, ?, ?, NOW())");
  $query->execute([$name, $slug, $content]);
  $postId = $pdo->lastInsertId();

  // Insertion des catégories dans la table post_category
  if (!empty($categories)) {
    $query = $pdo->prepare("INSERT INTO post_category (post_id, category_id) VALUES (?, ?)");
    foreach ($categories as $categoryId) {
      $query->execute([$postId, $categoryId]);
    }
  }

  header('Location: ' . $router->url('admin_posts'));
  exit();
}

// Récupération des catégories pour le select
$categories = $pdo->query("SELECT * FROM category ORDER BY name ASC")->fetchAll(PDO::FETCH_CLASS, \App\Model\Category::class);
?>

<div class="container">
  <h1>Créer un nouvel article</h1>
  <!-- Formulaire pour créer un nouvel article -->
  <form action="<?= $router->url('admin_post_create') ?>" method="POST">
    <div class="mb-3">
      <label for="name" class="form-label">Titre de l'article</label>
      <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="mb-3">
      <label for="slug" class="form-label">Slug</label>
      <input type="text" class="form-control" id="slug" name="slug" required>
    </div>

    <div class="mb-3">
      <label for="content" class="form-label">Contenu</label>
      <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
    </div>
    <!-- Select pour les catégories -->
    <div class="mb-3">
      <label for="categories" class="form-label">Catégories</label>
      <select class="form-select" id="categories" name="categories[]" multiple>
        <?php foreach ($categories as $category): ?>
          <option value="<?= $category->getId() ?>"><?= htmlentities($category->getName()) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="d-flex justify-content-between">
      <a href="<?= $router->url('admin_posts') ?>" class="btn btn-secondary">Retour</a>
      <button type="submit" class="btn btn-primary">Créer l'article</button>
    </div>
  </form>
</div>