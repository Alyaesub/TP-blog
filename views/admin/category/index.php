<?php
//feuille de code admin pour la gestion des catégories (index)
use App\Model\Category;
use App\ConnexionDb;

$pdo = ConnexionDb::getPdo();
$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_CLASS, Category::class);
$title = "Gestion des catégories";
?>

<div class="container">
  <h1>Administration des catégories</h1>
  <!-- Bouton pour créer une nouvelle catégorie -->
  <div class="d-flex justify-content-between my-4">
    <h2>Liste des catégories</h2>
    <a href="<?= $router->url('admin_category_new') ?>" class="btn btn-primary">Nouvelle catégorie</a><!--bouton pour créer une nouvelle catégorie et redirige vers la page admin/category/new.php-->
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $category): ?>
        <tr>
          <td>#<?= $category->getId() ?></td>
          <td><?= htmlentities($category->getName()) ?></td>
          <td>
            <a href="<?= $router->url('admin_category_edit', ['id' => $category->getId()]) ?>" class="btn btn-primary btn-sm">
              Éditer
            </a><!--bouton pour éditer une catégorie et redirige vers la page admin/category/edit.php-->
            <form action="<?= $router->url('admin_category_delete', ['id' => $category->getId()]) ?>" method="POST"
              style="display: inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
              <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
            </form><!--bouton pour supprimer une catégorie et redirige vers la page admin/category/delete.php-->
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>