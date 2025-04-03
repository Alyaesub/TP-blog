<?php

namespace App\Controller;
//feuille de code pour la connexion administrateur controller qui gere la connexion administrateur 


use App\ConnexionDb;
use App\Model\User;
use PDOException;

class LoginAdmin
{
  public function handle()
  {
    session_start();

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
      $pdo = ConnexionDb::getPdo();
      $userModel = new User($pdo);
      $user = $userModel->findByEmail($email);

      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: /admin');
        exit();
      } else {
        $_SESSION['login_error'] = "Identifiants invalides";
        header('Location: /admin/login');
        exit();
      }
    } catch (PDOException $e) {
      echo "Erreur de connexion à la base de données: " . $e->getMessage();
    }
  }
}
