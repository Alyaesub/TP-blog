<?php

use App\Controller\LoginAdmin;

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
$router->post('/admin/login', 'admin/loginAction', 'loginAdmin'); //

// Affiche le formulaire de login
$router->get('/admin/login', 'admin/login', 'admin_login'); //route pour la page login


// Routes pour l'administration
$router->get('/admin', 'admin/post/index', 'admin_posts'); //route pour la page admin
$router->get('/admin/post/new', 'admin/post/new', 'admin_post_new'); //route pour la page admin pour la creation d'un article
$router->post('/admin/post/new', 'admin/post/new', 'admin_post_create'); //route pour la page admin pour la creation d'un article 
$router->get('/admin/post/edit/[i:id]', 'admin/post/edit', 'admin_post_edit'); //route pour la page admin pour la modification d'un article 
$router->post('/admin/post/edit/[i:id]', 'admin/post/edit', 'admin_post_edit_post'); //route pour la page admin pour la modification d'un article 
$router->post('/admin/post/delete/[i:id]', 'admin/post/delete', 'admin_post_delete'); //route pour la page admin pour la suppression d'un article 

$router->run();
