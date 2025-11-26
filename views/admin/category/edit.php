<?php
//feuille de code admin pour la modification d'une catégorie 
use App\Model\Category;
use App\ConnexionDb;

global $router;
$pdo = ConnexionDb::getPdo();
$title = "Éditer la catégorie";
$id = $_GET['id'] ?? $_POST['id'] ?? null;


// Si le formulaire est soumis, on met à jour la catégorie dans la base de données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $slug = $_POST['slug'] ?? '';

  // Mise à jour de la catégorie dans la table category
  $query = $pdo->prepare("UPDATE category SET name = ?, slug = ? WHERE id = ?");
  $query->execute([$name, $slug, $id]);

  header('Location: ' . $router->url('admin_category')); //redirection vers la page admin catégories
  exit();
}


// Récupération de la catégorie à éditer
$query = $pdo->prepare("SELECT * FROM category WHERE id = ?");
$query->execute([$id]);
$category = $query->fetchObject(Category::class);

if (!$id || $category === false) {
  http_response_code(404);
  echo "<h1>Catégorie introuvable</h1>";
  exit;
}

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM category ORDER BY name ASC")->fetchAll(PDO::FETCH_CLASS, \App\Model\Category::class);

?>

<div class="container">
  <h1>Éditer la catégorie</h1>

  <form action="<?= $router->url('admin_category_edit_post', ['id' => $category->getId()]) ?>" method="POST">
    <input type="hidden" name="id" value="<?= $category->getId() ?>">
    <div class="mb-3">
      <label for="name" class="form-label">Titre de la catégorie</label>
      <input type="text" class="form-control" id="name" name="name" value="<?= htmlentities($category->getName()) ?>" required>
    </div>

    <div class="mb-3">
      <label for="slug" class="form-label">Slug</label>
      <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlentities($category->getSlug()) ?>" required>
    </div>


    <div class="d-flex justify-content-between">
      <a href="<?= $router->url('admin_category') ?>" class="btn btn-secondary">Retour</a>
      <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </div>
  </form>
</div>