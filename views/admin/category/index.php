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
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>

</div>