<?php

require '../vendor/autoload.php';

//donne l'heure avec miliseconde pour voir le chargement du site
define('DEBUG_TIME', microtime(true));

//systeme et outils de debug "whoops"
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


$router = new App\Router(dirname(__DIR__) . '/views');
$router->get('/', 'post/index', 'home');
$router->get('/blog/categories/[*:slug]?', 'category/show', 'categories');
$router->run();
