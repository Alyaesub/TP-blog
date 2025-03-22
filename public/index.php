
<?php

require '../vendor/autoload.php';

$router = new AltoRouter();


$router->map('GET', '/blog', function () {
  require dirname(__DIR__) . '/views/post/index.php';
});
$router->map('GET', '/blog/categories', function () {
  require dirname(__DIR__) . '/views/category/show.php';
});
$match = $router->match();
if ($match) {
  call_user_func_array($match['target'], $match['params']);
} else {
  echo "Page introuvable";
}
