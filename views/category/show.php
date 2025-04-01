<?php

use App\ConnexionDb;
use App\Model\Category;
use App\Model\Post;
//ON RECOPIE LE CODE DE POST/SHOW.PHP car la logique est la même  

$id = (int)$params['id']; //convertit la valeur de la clé id de l'url en nombre entier 
$slug = $params['slug']; //recupère la valeur de la clé slug de l'url 

//requete pour récupérer la categorie 
$pdo = ConnexionDb::getPdo(); //appel de la fonction getPdo de la classe ConnexionDb pour la connexion à la base de données 
$query = $pdo->prepare("SELECT * FROM category WHERE id = :id"); // requête préparée pour récupérer une categorie par son id 
$query->execute(['id' => $id]); // exécute la requête préparée avec l'id de la route  
$query->setFetchMode(PDO::FETCH_CLASS, Category::class); // on ce sert de fetchMOde pour utilser la classe Post   car fetch seul prend pas les caractères 
$category = $query->fetch(); // récupère les categories  de la base de données avec la classe Category 
/* dd($category); */

if ($category === false) {
  throw new Exception('Aucune categorie trouvée');
}

// si le slug de la route ne correspond pas au slug du post, on redirige vers la page du post avec l'id et le slug
if ($category->getSlug() !== $slug) {
  header('Location: /blog/categories/' . $category->getSlug() . '-' . $category->getId());
  http_response_code(301);
  exit;
}
/* dd($category);
 */
$title = htmlentities($category->getName()); /* donne le titre de la categorie a l'url */
//verifie si l'url contient la page et si c'est le cas, on l'affiche, sinon on affiche la page 1
$page = $_GET['page'] ?? 1;

if (!filter_var($page, FILTER_VALIDATE_INT)) {
  throw new Exception('Numéro de la page invalide'); //si l'url de la page n'est pas un nombre entier, on affiche une erreur evite les failles de sécurité  comme les injections sql  
}

if ($page === '1') {
  header('Location: /'); //si l'url de la page est 1, on redirige vers la page 1  sans passer par la page 1 dans l'url  
  exit;
}
$currentPage = (int)$page;
if ($currentPage <= 0) {
  throw new Exception('Page invalide');
}
$count = (int)$pdo
  ->query('SELECT COUNT(post_id) FROM post_category WHERE category_id = ' . $category->getId())
  ->fetch(PDO::FETCH_NUM)[0]; //recupère le nombre de post
$perPage = 12; //limit le nombre d'article par page a 12 
$pages = ceil($count / $perPage); // nombre d'articles par page
if ($currentPage > $pages) {
  throw new Exception('cette page n\'existe pas');
}
//requete pour récupérer les posts de la categorie
$offset = ($currentPage - 1) * $perPage;
$query = $pdo->query(
  "SELECT p.* 
  FROM post p 
  JOIN post_category pc ON p.id = pc.post_id
  WHERE pc.category_id = " . $category->getId() . "
  ORDER BY p.created_at DESC 
  LIMIT $perPage OFFSET $offset"
);
$posts = $query->fetchAll(PDO::FETCH_CLASS, Post::class);
?>

<h1>Ma categorie est : <?= htmlentities($category->getName()); ?></h1>
<h2>Voici les posts de cette categorie :</h2>