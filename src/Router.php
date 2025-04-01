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

  public function __construct(string $viewPath) //constructeur de la classe Router  
  {
    $this->viewPath = $viewPath; //assigne la valeur de la variable viewPath à la propriété viewPath de l'objet  
    $this->router = new \AltoRouter(); //instanciation de la classe AltoRouter pour la gestion des routes 
  }
  public function get(string $url, string $view, ?string $name = null) //méthode pour ajouter une route GET
  {
    $this->router->map('GET', $url, $view, $name); //ajoute une route GET à la variable router   

    return $this;
  }
  public function run(): self //méthode pour exécuter la route 
  {
    $match = $this->router->match(); //recupère la route matchée de la variable router 
    $params = $match['params']; //recupère les paramètres de la route matchée  
    /*     dd($match);
 */
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
