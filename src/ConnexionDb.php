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


/* //classe pour la connexion à la base de données Pour O2switch
class ConnexionDb
{
  public static function getPdo(): PDO
  {
    $env = parse_ini_file(__DIR__ . '/../env.ini', true);
    $host = $env['database']['DB_HOST'];
    $dbname = $env['database']['DB_NAME'];
    $user = $env['database']['DB_USER'];
    $password = $env['database']['DB_PASSWORD'];

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    return $pdo;
  }
} */
