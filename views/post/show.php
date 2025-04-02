<!-- feuille de code pour la page show.php qui affiche un post -->
<?php
/* dd($params);
 */

use App\ConnexionDb;
use App\Model\Post;
use App\Model\Category;

$id = (int)$params['id']; //convertit la valeur de la clé id de l'url en nombre entier 
$slug = $params['slug']; //recupère la valeur de la clé slug de l'url 

//requete pour récupérer le post
$pdo = ConnexionDb::getPdo(); //appel de la fonction getPdo de la classe ConnexionDb pour la connexion à la base de données 
$query = $pdo->prepare("SELECT * FROM post WHERE id = :id"); // requête préparée pour récupérer un post par son id 
$query->execute(['id' => $id]); // exécute la requête préparée avec l'id de la route  
$query->setFetchMode(PDO::FETCH_CLASS, Post::class); // on ce sert de fetchMOde pour utilser la classe Post   car fetch seul prend pas les caractères 
$post = $query->fetch(); // récupère les posts de la base de données avec la classe Post 
/* dd($post); */

if ($post === false) {
  throw new Exception('Aucun post trouvé');
}

// si le slug de la route ne correspond pas au slug du post, on redirige vers la page du post avec l'id et le slug
if ($post->getSlug() !== $slug) {
  header('Location: /blog/' . $post->getSlug() . '-' . $post->getId());
  http_response_code(301);
  exit;
}

//requete pour récupérer les catégories
$query = $pdo->prepare("SELECT c.id, c.name, c.slug /* je recuper que les id name et slug */
FROM post_category pc 
JOIN category c ON c.id = pc.category_id  /* je join la table category avec la table post_category */
WHERE pc.post_id = :id"); //"pc" est l'alias de la table post_category et "c" est l'alias de la table category 
$query->execute(['id' => $post->getId()]);
$query->setFetchMode(PDO::FETCH_CLASS, Category::class); //pareil que pour le post
$categories = $query->fetchAll();
/* dd($categories); */
?>
<h1>Nom du post : <?= htmlentities($post->getName()); ?></h1>
<?php $title = htmlentities($post->getName()); ?><!--  donne le titre du post a l'url -->
<div class="card-body">
  <h1 class="card-title"><?= htmlentities($post->getName()) ?></h1> <!-- affiche le nom du post -->
  <p class="text-muted"><?= $post->getCreatedAt()->format('d F Y') ?></p> <!-- affiche la date de création du post -->
  <p class="card-text"><?= $post->getContent() ?></p> <!-- affiche le contenu du post -->
</div>

<div>
  <?php
  $categoriesCount = count($categories);
  foreach ($categories as $index => $category) { ?>
    <a href="/blog/categories/<?= $category->getSlug() ?>-<?= $category->getId() ?>"><?= $category->getName() ?></a>
    <?php if ($index < $categoriesCount - 1) { ?>, <?php } ?>
<?php } ?>
</div>