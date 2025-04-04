<?php
//feuille de code admin pour la suppression d'une catégorie  
use App\ConnexionDb;

$pdo = ConnexionDb::getPdo();

// Suppression des relations avec les catégories
$query = $pdo->prepare("DELETE FROM post_category WHERE category_id = ?");
$query->execute([$params['id']]);

// Suppression de la catégorie
$query = $pdo->prepare("DELETE FROM category WHERE id = ?");
$query->execute([$params['id']]);

// Redirection vers la liste des catégories
header('Location: ' . $router->url('admin_category') . '?delete=1');
exit();
