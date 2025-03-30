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
      <?php require dirname(__DIR__) . '/layouts/card.php' ?>
    </div>
  <?php endforeach; ?>
</div>