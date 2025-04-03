<?php

namespace App\Model;

use App\ConnexionDb;
use PDO;
// permet de récupérer les données de la base de données et de créer des objets User  
class User
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = ConnexionDb::getPdo();
  }

  public function findByEmail(string $email): ?array
  {
    $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
  }
}
