<?php

require '../vendor/autoload.php';

//donne l'heure avec miliseconde pour voir le chargement du site
define('DEBUG_TIME', microtime(true));

//systeme et outils de debug "whoops"
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

//routeur qui permet de gérer les routes menant vers les pages du site
$router = new App\Router(dirname(__DIR__) . '/views'); //chemin vers le dossier views
$router->get('/', 'post/index', 'home'); //route pour la page d'accueil    
$router->get('/blog', 'post/index', 'blog'); //route pour la page blog
$router->get('/blog/categories/[*:slug]-[i:id]', 'category/show', 'categories'); //route pour la page categories
$router->get('/blog/[*:slug]-[i:id]', 'post/show', 'post'); //route pour la page post
$router->get('/admin', 'admin/indexAdmin', 'admin'); //route pour la page admin a lesser le temps du teste
$router->get('/admin/login', 'admin/login', 'admin_login'); //route pour la page admin/login pour la connexion  

$router->post('/admin/login', 'admin/loginHandler', 'admin_login_post');

// Routes pour l'administration des posts
$router->get('/admin/posts', 'admin/post/index', 'admin_posts'); //route pour la page admin posts
$router->get('/admin/post/new', 'admin/post/new', 'admin_post_new'); //route pour la page admin pour la creation d'un article
$router->post('/admin/post/new', 'admin/post/new', 'admin_post_create'); //route pour la page admin pour la creation d'un article 
$router->get('/admin/post/edit/[i:id]', 'admin/post/edit', 'admin_post_edit'); //route pour la page admin pour la modification d'un article 
$router->post('/admin/post/edit/[i:id]', 'admin/post/edit', 'admin_post_edit_post'); //route pour la page admin pour la modification d'un article 
$router->post('/admin/post/delete/[i:id]', 'admin/post/delete', 'admin_post_delete'); //route pour la page admin pour la suppression d'un article 

// Routes admin pour les catégories
$router->get('/admin/categories', 'admin/category/index', 'admin_category'); //route pour la page admin categories
$router->get('/admin/categories/new', 'admin/category/new', 'admin_category_new'); //route pour la page admin pour la creation d'une categorie
$router->post('/admin/categories/new', 'admin/category/new', 'admin_category_create'); //route pour la page admin pour la creation d'une categorie
$router->get('/admin/categories/edit/[i:id]', 'admin/category/edit', 'admin_category_edit'); //route pour la page admin pour la modification d'une categorie
$router->post('/admin/categories/edit/[i:id]', 'admin/category/edit', 'admin_category_edit_post'); //route pour la page admin pour la modification d'une categorie
$router->post('/admin/categories/delete/[i:id]', 'admin/category/delete', 'admin_category_delete'); //route pour la page admin pour la suppression d'une categorie

$router->run();
