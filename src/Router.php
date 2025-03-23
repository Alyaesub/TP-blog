<?php

namespace App;

class Router
{

  /**
   * @var string
   */
  private $viewPath;

  /**
   * @var altorouteur
   */
  private $router;

  public function __construct(string $viewPath)
  {
    $this->viewPath = $viewPath;
    $this->router = new \AltoRouter();
  }
  public function get(string $url, string $view, ?string $name = null)
  {
    $this->router->map('GET', $url, $view, $name);

    return $this;
  }
  public function run(): self
  {
    $match = $this->router->match();

    // Vérifie si une route correspond
    if (!$match) {
      http_response_code(404);
      echo "Erreur 404 - Page introuvable";
      return $this;
    }

    // Vérifie si 'target' existe bien dans la route matchée
    if (!isset($match['target']) || empty($match['target'])) {
      http_response_code(500);
      echo "Erreur 500 - Vue non définie pour cette route";
      return $this;
    }

    $views = $match['target'];
    ob_start();
    require $this->viewPath . DIRECTORY_SEPARATOR . $views . '.php';
    $content = ob_get_clean();
    require $this->viewPath . DIRECTORY_SEPARATOR . 'layouts/default.php';

    return $this;
  }
}
