<?php
//feuille de code admin pour la gestion des articles (index)
use App\Model\Post;
use App\ConnexionDb;

$pdo = ConnexionDb::getPdo();
$posts = $pdo->query("SELECT * FROM post ORDER BY created_at DESC")->fetchAll(PDO::FETCH_CLASS, Post::class);
$title = "Gestion des articles";
?>

<div class="container">
  <h1>Administration des articles</h1>
  <!-- Bouton pour créer un nouvel article -->
  <div class="d-flex justify-content-between my-4">
    <h2>Liste des articles</h2>
    <a href="<?= $router->url('admin_post_new') ?>" class="btn btn-primary">Nouvel article</a>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Date de création</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $post): ?>
        <tr>
          <td>#<?= $post->getId() ?></td>
          <td><?= htmlentities($post->getName()) ?></td>
          <td><?= $post->getCreatedAt()->format('d/m/Y H:i') ?></td>
          <td>
            <a href="<?= $router->url('admin_post_edit', ['id' => $post->getId()]) ?>" class="btn btn-primary btn-sm">
              Éditer
            </a>
            <form action="<?= $router->url('admin_post_delete', ['id' => $post->getId()]) ?>" method="POST"
              style="display: inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
              <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>