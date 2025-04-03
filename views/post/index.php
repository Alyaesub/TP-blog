<?php
// j'importe le namespace Text pour la fonction text  
use App\Helpers\Text;
// j'importe le namespace Post
use App\Model\Post;
// j'importe le namespace ConnexionDb 
use App\ConnexionDb;

$title = 'mon blog';
$pdo = ConnexionDb::getPdo(); //appel de la fonction getPdo de la classe ConnexionDb pour la connexion à la base de données 

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM category ORDER BY name ASC")->fetchAll(PDO::FETCH_CLASS, \App\Model\Category::class);

// Récupération des derniers articles (limité à 3)
$latestPosts = $pdo->query("SELECT * FROM post ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_CLASS, Post::class);

//verifie si l'url contient la page et si c'est le cas, on l'affiche, sinon on affiche la page 1
$page = $_GET['page'] ?? 1;

if (!filter_var($page, FILTER_VALIDATE_INT)) {
  throw new Exception('Numéro de la page invalide'); //si l'url de la page n'est pas un nombre entier, on affiche une erreur evite les failles de sécurité  comme les injections sql  
}

$currentPage = (int)$page;
if ($currentPage <= 0) {
  throw new Exception('Page invalide');
}
$count = (int)$pdo->query('SELECT COUNT(id) FROM post')->fetch(PDO::FETCH_NUM)[0]; //recupère le nombre de post
$perPage = 12; //limit le nombre d'article par page a 12 
$pages = ceil($count / $perPage); // nombre d'articles par page
if ($currentPage > $pages) {
  throw new Exception('cette page n\'existe pas');
}
$offset = ($currentPage - 1) * $perPage;
$query = $pdo->prepare("SELECT * FROM post ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$query->bindValue(':limit', $perPage, PDO::PARAM_INT);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$posts = $query->fetchAll(PDO::FETCH_CLASS, Post::class);

if (empty($posts)) {
  throw new Exception('Aucun article trouvé');
}
?>
<div class="container">
  <div class="row">
    <!-- Section des derniers articles -->
    <div class="col-md-8">
      <h2 class="mb-4">Derniers articles</h2>
      <div class="row">
        <?php foreach ($latestPosts as $post) : ?>
          <div class="col-md-4 mb-4">
            <?php require dirname(__DIR__) . '/layouts/card.php' ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Section des catégories -->
    <div class="col-md-4">
      <h2 class="mb-4">Catégories</h2>
      <div class="list-group">
        <?php foreach ($categories as $category) : ?>
          <a href="/blog/categories/<?= $category->getSlug() ?>-<?= $category->getId() ?>"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <?= htmlentities($category->getName()) ?>
            <span class="badge bg-primary rounded-pill">
              <?= $pdo->query("SELECT COUNT(*) FROM post_category WHERE category_id = " . $category->getId())->fetchColumn() ?>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Section de tous les articles avec pagination -->
  <div class="row mt-5">
    <div class="col-12">
      <h2 class="mb-4">Tous les articles</h2>
      <div class="row">
        <?php foreach ($posts as $post) : ?>
          <div class="col-md-3">
            <?php require dirname(__DIR__) . '/layouts/card.php' ?>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="d-flex justify-content-between my-4">
        <?php if ($currentPage > 1) : ?>
          <a href="?page=<?= $currentPage - 1 ?>" class="btn btn-primary">Page précédente</a>
        <?php endif; ?>
        <?php if ($currentPage < $pages) : ?>
          <a href="?page=<?= $currentPage + 1 ?>" class="btn btn-primary ms-auto">Page suivante</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>