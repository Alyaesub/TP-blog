<?php

use App\Helpers\Text;
// j'importe le namespace Post
use App\Model\Post;

$title = 'mon blog';
$pdo = new PDO('mysql:host=127.0.0.1;port=8889;dbname=TP_blog', 'root', 'root', [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
$query = $pdo->query('SELECT * FROM post ORDER BY created_at DESC LIMIT 12');
$posts = $query->fetchAll(PDO::FETCH_CLASS, Post::class);
?>
<h1>Mon Blog</h1>



<div class="row">
  <?php foreach ($posts as $post) : ?> <!-- boucle qui apelle les posts -->
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><?= htmlentities($post->getName()) ?></h5>
          <p class="card-text"><?= $post->getExcerpt() ?></p>
          <p>
            <a href="#" class="btn btn-primary">Voir plus</a>
          </p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>