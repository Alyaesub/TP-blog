<?php

namespace App;

use PDO; //importation de la classe PDO pour la connexion à la base de données   

//classe pour la connexion à la base de données utilisée dans les controllers et les models 
class ConnexionDb
{
  private static ?PDO $pdo = null;

  public static function getPdo(): PDO
  {
    if (self::$pdo instanceof PDO) {
      return self::$pdo;
    }

    $envPath = __DIR__ . '/../.env';
    $config = parse_ini_file($envPath);

    if ($config === false) {
      throw new \RuntimeException('Impossible de lire le fichier .env');
    }

    $dsn = $config['DB_DSN'] ?? null;
    $user = $config['DB_USER'] ?? null;
    $password = $config['DB_PASSWORD'] ?? null;

    if (!$dsn || !$user) {
      throw new \RuntimeException('Configuration base de données manquante dans .env');
    }

    self::$pdo = new PDO($dsn, $user, $password ?? '', [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    return self::$pdo;
  }
}
