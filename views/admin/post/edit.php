<?php

use App\Model\Post;
use App\ConnexionDb;

global $router;
$pdo = ConnexionDb::getPdo();
$title = "Éditer l'article";
$id = $_GET['id'] ?? $_POST['id'] ?? null;

/* var_dump($_POST);s
 */ // Si le formulaire est soumis, on met à jour l'article dans la base de données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $slug = $_POST['slug'] ?? '';
  $content = $_POST['content'] ?? '';
  $categories = $_POST['categories'] ?? [];

  // Mise à jour de l'article dans la table post
  $query = $pdo->prepare("UPDATE post SET name = ?, slug = ?, content = ? WHERE id = ?");
  $query->execute([$name, $slug, $content, $id]);

  // Suppression des anciennes catégories
  $query = $pdo->prepare("DELETE FROM post_category WHERE post_id = ?");
  $query->execute([$id]);

  // Insertion des nouvelles catégories dans la table post_category 
  if (!empty($categories)) {
    $query = $pdo->prepare("INSERT INTO post_category (post_id, category_id) VALUES (?, ?)");
    foreach ($categories as $categoryId) {
      $query->execute([$id, $categoryId]);
    }
  }

  header('Location: ' . $router->url('admin_posts'));
  exit;
}


// Récupération de l'article à éditer
$query = $pdo->prepare("SELECT * FROM post WHERE id = ?");
$query->execute([$id]);
$post = $query->fetchObject(Post::class);

if (!$id || $post === false) {
  http_response_code(404);
  echo "<h1>Article introuvable</h1>";
  exit;
}

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM category ORDER BY name ASC")->fetchAll(PDO::FETCH_CLASS, \App\Model\Category::class);

// Récupération des catégories de l'article
$query = $pdo->prepare("SELECT category_id FROM post_category WHERE post_id = ?");
$query->execute([$post->getId()]);
$postCategories = $query->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container">
  <h1>Éditer l'article</h1>

  <form action="<?= $router->url('admin_post_edit_post', ['id' => $post->getId()]) ?>" method="POST">
    <input type="hidden" name="id" value="<?= $post->getId() ?>">
    <div class="mb-3">
      <label for="name" class="form-label">Titre de l'article</label>
      <input type="text" class="form-control" id="name" name="name" value="<?= htmlentities($post->getName()) ?>" required>
    </div>

    <div class="mb-3">
      <label for="slug" class="form-label">Slug</label>
      <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlentities($post->getSlug()) ?>" required>
    </div>

    <div class="mb-3">
      <label for="content" class="form-label">Contenu</label>
      <textarea class="form-control" id="content" name="content" rows="10" required><?= htmlentities($post->getContent()) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="categories" class="form-label">Catégories</label>
      <select class="form-select" id="categories" name="categories[]" multiple>
        <?php foreach ($categories as $category): ?>
          <option value="<?= $category->getId() ?>" <?= in_array($category->getId(), $postCategories) ? 'selected' : '' ?>>
            <?= htmlentities($category->getName()) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="d-flex justify-content-between">
      <a href="<?= $router->url('admin_posts') ?>" class="btn btn-secondary">Retour</a>
      <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </div>
  </form>
</div>