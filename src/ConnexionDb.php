<?php

namespace App;

use PDO; //importation de la classe PDO pour la connexion à la base de données   

//classe pour la connexion à la base de données utilisée dans les controllers et les models 
class ConnexionDb
{
  public static function getPdo(): PDO
  {
    $pdo = new PDO('mysql:host=127.0.0.1;port=8889;dbname=TP_blog', 'root', 'root', [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    return $pdo;
  }
}
