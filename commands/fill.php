<?php
//ce fichier permet de remplir la base de données avec des données fictives pour les tests  

//inclusion de faker pour la génération de données fictives 
require_once __DIR__ . '/../vendor/autoload.php'; // Inclure Faker correctement

$faker = Faker\Factory::create(); // Initialisation de Faker

//connexion à la base de données  
use App\ConnexionDb;

try {
  $pdo = ConnexionDb::getPdo();
  echo "✅ Connexion réussie !";
} catch (PDOException $e) {
  die("❌ Erreur de connexion : " . $e->getMessage());
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE post_category');
$pdo->exec('TRUNCATE TABLE post');
$pdo->exec('TRUNCATE TABLE category');
$pdo->exec('TRUNCATE TABLE user');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$posts = [];
$categories = [];

//création de fake data avec faker.php pour la table "post"
for ($i = 0; $i < 50; $i++) {
  $name = $faker->sentence();
  $slug = $faker->slug;
  $created_at = $faker->date('Y-m-d') . ' ' . $faker->time('H:i:s');
  $content = $faker->paragraphs(rand(3, 15), true);

  $query = "INSERT INTO post (name, slug, created_at, content) VALUES (:name, :slug, :created_at, :content)";
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'name' => $name,
    'slug' => $slug,
    'created_at' => $created_at,
    'content' => $content
  ]);
  $posts[] = $pdo->lastInsertId();
};

//Création de 5 catégories avec Faker pour la table category
for ($i = 0; $i < 5; $i++) {
  $cat_name = ucfirst($faker->word);  // Génère un mot et met la première lettre en majuscule
  $cat_slug = $faker->slug;

  $query = "INSERT INTO category (name, slug) VALUES (:name, :slug)";
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'name' => $cat_name,
    'slug' => $cat_slug
  ]);
  $categories[] = $pdo->lastInsertId();
}

// On associe aléatoirement des articles à des catégories
foreach ($posts as $post) {
  $randomCategories = $faker->randomElements($categories, rand(0, count($categories)));
  foreach ($randomCategories as $category) {
    $pdo->exec("INSERT INTO post_category SET post_id=$post, category_id=$category");
  }
}
