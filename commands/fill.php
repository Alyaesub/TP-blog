<?php
//ce fichier permet de remplir la base de données avec des données fictives pour les tests 
//et de crée les data que je veux en paramettrant fakers 

//inclusion de faker pour la génération de données fictives 
require_once __DIR__ . '/../vendor/autoload.php'; // Inclure Faker correctement

//connexion à la base de données  
use App\ConnexionDb;
use App\Helpers\Text;

//je teste la connexion à la base de données 
try {
  $pdo = ConnexionDb::getPdo();
  echo "✅ Connexion réussie !";
} catch (PDOException $e) {
  die("❌ Erreur de connexion : " . $e->getMessage());
}


//je supprime les data de la base de données pour les remplacer par des données fictives 
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE post_category');
$pdo->exec('TRUNCATE TABLE post');
$pdo->exec('TRUNCATE TABLE category');
$pdo->exec('TRUNCATE TABLE user');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

//je paramttre faker pour la génération de données fictives 
$faker = Faker\Factory::create('fr_FR'); // Initialisation de Faker pour la génération de données fictives en français

$post_names = ['développement web', 'blockchain', 'smart contracts', 'PHP', 'JavaScript', 'Python', 'Olidity', 'NFT', 'dApps', 'sécurité web']; //je crée un tableau pour stocker les noms des posts 
$category_names = ['Développement Web', 'Blockchain', 'Cryptomonnaies', 'Front End', 'Back End']; //je crée un tableau pour stocker les noms des catégories

$post_ids = []; // Tableau pour stocker les IDs des posts créés

//création de fake data avec faker.php pour la table "post"
for ($i = 0; $i < 50; $i++) {
  $post_name = $faker->randomElement($post_names); //je choisis un post aléatoire parmi les posts disponibles 
  $name = ucfirst($faker->catchPhrase) . " et " . $post_name; //je crée un nom de post avec un catchphrase et un post aléatoire  
  $slug = Text::slugify($name); //je crée un slug pour le post 
  $created_at = $faker->date('Y-m-d') . ' ' . $faker->time('H:i:s');
  $content = "Dans cet article, nous allons parler de $post_name. " . $faker->paragraphs(rand(3, 6), true); //je crée un contenu pour le post 

  $query = "INSERT INTO post (name, slug, created_at, content) VALUES (:name, :slug, :created_at, :content)";
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'name' => $name,
    'slug' => $slug,
    'created_at' => $created_at,
    'content' => $content
  ]);
  $post_ids[] = $pdo->lastInsertId();
};

//Création de 5 catégories avec Faker pour la table category
$category_ids = []; // Tableau pour stocker les IDs des catégories
for ($i = 0; $i < 5; $i++) {
  $cat_name = $faker->randomElement($category_names); //je choisis une catégorie aléatoire parmi les catégories disponibles   
  $cat_slug = Text::slugify($cat_name);

  $query = "INSERT INTO category (name, slug) VALUES (:name, :slug)";
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'name' => $cat_name,
    'slug' => $cat_slug
  ]);
  $category_ids[] = $pdo->lastInsertId();
}

// On associe aléatoirement des articles à des catégories
$stmt = $pdo->prepare("INSERT INTO post_category (post_id, category_id) VALUES (:post_id, :category_id)");
foreach ($post_ids as $post_id) {
  // On s'assure qu'un post a au moins une catégorie en utilisant rand(1, count($category_ids))
  $randomCategories = $faker->randomElements($category_ids, rand(1, count($category_ids)));
  foreach ($randomCategories as $category_id) {
    $stmt->execute([
      'post_id' => $post_id,
      'category_id' => $category_id
    ]);
  }
}
