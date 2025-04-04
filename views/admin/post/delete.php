<?php
//feuille de code admin pour la suppression d'un article  
use App\ConnexionDb;

$pdo = ConnexionDb::getPdo();

// Suppression des relations avec les catégories
$query = $pdo->prepare("DELETE FROM post_category WHERE post_id = ?");
$query->execute([$params['id']]);

// Suppression de l'article
$query = $pdo->prepare("DELETE FROM post WHERE id = ?");
$query->execute([$params['id']]);

// Redirection vers la liste des articles
header('Location: ' . $router->url('admin_posts') . '?delete=1');
exit();
