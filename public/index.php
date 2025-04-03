<?php

require '../vendor/autoload.php';

//donne l'heure avec miliseconde pour voir le chargement du site
define('DEBUG_TIME', microtime(true));

//systeme et outils de debug "whoops"
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

//routeur qui permet de gérer les routes menant vers les pages du site
$router = new App\Router(dirname(__DIR__) . '/views');
$router->get('/', 'post/index', 'home');
$router->get('/blog', 'post/index', 'blog');
$router->get('/blog/categories/[*:slug]-[i:id]', 'category/show', 'categories');
$router->get('/blog/[*:slug]-[i:id]', 'post/show', 'post');
$router->get('/admin/login', 'admin/login', 'admin_login');

// Routes pour l'administration
$router->get('/admin', 'admin/post/index', 'admin_posts');
$router->get('/admin/post/new', 'admin/post/new', 'admin_post_new');
$router->post('/admin/post/new', 'admin/post/new', 'admin_post_create');
$router->get('/admin/post/edit/[i:id]', 'admin/post/edit', 'admin_post_edit');
$router->post('/admin/post/edit/[i:id]', 'admin/post/edit', 'admin_post_edit_post');
$router->post('/admin/post/delete/[i:id]', 'admin/post/delete', 'admin_post_delete');

$router->run();
